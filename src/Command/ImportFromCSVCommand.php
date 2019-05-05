<?php
namespace App\Command;

use App\Entity\Account;
use App\Entity\Currency;
use App\Entity\Operation;
use App\Entity\Payment;
use App\Entity\Tag;
use App\Repository\AccountRepository;
use App\Repository\CurrencyRepository;
use App\Repository\OperationRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Exception\RuntimeException;

final class ImportFromCSVCommand extends Command
{

    private $fileLinesCount = 0;

    private $delimiter = ';';

    private $accounts;

    private $tags;

    private $currencies;

    private $operations;

    private $em;

    private $user;

    /**
     *
     * @param EntityManagerInterface $em
     * @param TagRepository $tags
     * @param CurrencyRepository $currencies
     * @param AccountRepository $accounts
     * @param OperationRepository $operations
     * @param UserRepository $user
     */
    public function __construct(EntityManagerInterface $em, TagRepository $tags, CurrencyRepository $currencies, AccountRepository $accounts, OperationRepository $operations, UserRepository $user)
    {
        parent::__construct(null);
        //TODO может убрать вызов загрузку конкретных реп из конструктора, а сделать вызов руками из $em?
        $this->accounts = $accounts;
        $this->tags = $tags;
        $this->currencies = $currencies;
        $this->operations = $operations;

        $this->em = $em;

        // TODO ask and check user
        $this->user = $user->find(1);
        if (! $this->user) {
            throw new RuntimeException('User not found');
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure(): void
    {
        $this->setName('almo:import-data-from-csv')
            ->setDescription('Import wallet data from the csv-backup')
            ->addArgument("file", InputArgument::REQUIRED, "Path of the CSV file")
            ->setHelp($this->getCommandHelp());
    }

    /**
     *
     * {@inheritdoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $filePath = $input->getArgument("file");
        $this->fileLinesCount = self::getLinesCount($filePath);
        $this->delimiter = self::guessDelimiter($filePath);

        $this->validateDictionariesFromFile($filePath, $input, $output);
        $this->loadOperationFromFile($filePath);
    }

    /**
     *
     * @param string $path
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function validateDictionariesFromFile(string $path, InputInterface $input, OutputInterface $output): void
    {
        $dictionaries = $this->loadUniqueDictionariesFromFile($path);
        $newDictionaries = $this->getNewdDictionaries($dictionaries);
        $this->createDictionariesIfNeeded($newDictionaries, $input, $output);
    }

    /**
     *
     * @param string $path
     * @return array
     */
    protected function loadUniqueDictionariesFromFile(string $path): array
    {
        $fileHandler = fopen($path, "r");

        $tags = [];
        $currencies = [];
        $accounts = [];

        $this->io->writeln('Validate dictionaries from csv file');
        $this->io->progressStart($this->fileLinesCount);

        while (($data = fgetcsv($fileHandler, 4096, $this->delimiter)) !== FALSE) {
            [
                $tag,
                $currency,
                $account
            ] = self::parseRow($data);

            if (! in_array($tag, $tags)) {
                $tags[] = $tag;
            }
            if (! in_array($currency, $currencies)) {
                $currencies[] = $currency;
            }
            if (! in_array($account, $accounts)) {
                $accounts[] = $account;
            }

            $this->io->progressAdvance();
        }

        $this->io->progressFinish();

        fclose($fileHandler);

        return [
            $tags,
            $currencies,
            $accounts
        ];
    }

    /**
     * 
     * @param string $path
     * @throws RuntimeException
     */
    protected function loadOperationFromFile(string $path): void
    {
        $fileHandler = fopen($path, "r");
        $cashedOperations = [];

        $this->io->writeln('Read operations from csv file');
        $this->io->progressStart($this->fileLinesCount);

        $this->em->beginTransaction();

        while (($data = fgetcsv($fileHandler, 4096, $this->delimiter)) !== FALSE) {
            [
                $rawTag,
                $rawCurrency,
                $rawAccount,
                $rawOperation,
                $rawPayment
            ] = self::parseRow($data);

            if ($rawOperation['type'] === 'transfer') {
                $tag = null;
            } else {
                $tag = $this->tags->findOneBy([
                    'title' => $rawTag['title'],
                    'payType' => $rawTag['payType']
                ]);
                if (! $tag) {
                    throw new RuntimeException("Can't found a tag");
                }
            }

            $currency = $this->currencies->findOneBy([
                'title' => $rawCurrency['title']
            ]);
            if (! $currency) {
                throw new RuntimeException("Can't found a currency");
            }

            $account = $this->accounts->findOneBy([
                'title' => $rawAccount['title']
            ]);
            if (! $account) {
                throw new RuntimeException("Can't found an account");
            }

            if (! isset($cashedOperations[$rawOperation['id']])) {
                $operation = new Operation();
                $operation->setTagId($tag);
                $operation->setUserId($this->user);
                $operation->setDate($rawOperation['date']);
                $operation->setTitle($rawOperation['title']);
                $operation->setType($rawOperation['type']);
                $operation->setNotice($rawOperation['notice']);

                $cashedOperations[$rawOperation['id']] = $operation;
            } else {
                $operation = $cashedOperations[$rawOperation['id']];
            }

            $payment = new Payment();
            $payment->setCurrencyId($currency);
            $payment->setAmount($rawPayment['amount']);
            $payment->setAccountId($account);
            $payment->setOperationId($operation);

            $this->em->persist($operation);
            $this->em->persist($payment);

            $this->io->progressAdvance();
        }

        $this->io->progressFinish();

        $this->io->writeln('Try to commit in database');
        $this->em->flush();
        $this->em->commit();
        $this->io->success('Operations were loaded');

        fclose($fileHandler);
    }

    /**
     *
     * @param array $newEntities
     * @return array
     */
    protected function getNewdDictionaries(array $newEntities): array
    {
        [
            $tags,
            $currencies,
            $accounts
        ] = $newEntities;
        $newTags = [];
        $newCurrencies = [];
        $newAccounts = [];

        foreach ($tags as $tag) {

            if ((self::toNullIfNeeded($tag['title']) !== null) && (! $this->tags->findOneBy([
                'title' => $tag['title'],
                'payType' => $tag['payType']
            ]))) {
                $newTags[] = $tag;
            }
        }

        foreach ($currencies as $currency) {

            if (! $this->currencies->findOneBy([
                'title' => $currency['title']
            ])) {
                $newCurrencies[] = $currency;
            }
        }

        foreach ($accounts as $account) {

            if (! $this->accounts->findOneBy([
                'title' => $account['title']
            ])) {
                $newAccounts[] = $account;
            }
        }

        return [
            $newTags,
            $newCurrencies,
            $newAccounts
        ];
    }

    /**
     *
     * @param array $newEntities
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function createDictionariesIfNeeded(array $newEntities, InputInterface $input, OutputInterface $output): void
    {
        $approveToCreate = null;
        // TODO убрать хардкод массива и более корректно проверять наличие на изменение
        if (empty($newEntities[0]) && empty($newEntities[1]) && empty($newEntities[2])) {
            $this->io->writeln('All entities are in database');
            return;
        }

        // TODO need to assert anv validate
        [
            $newTags,
            $newCurrencies,
            $newAccounts
        ] = $newEntities;

        $this->io->writeln('Found several uncreated entities');

        if (! empty($newTags)) {
            $this->io->writeln('Tags:');
            foreach ($newTags as $newTag) {
                $this->io->writeln('title=' . $newTag['title'] . ', payType=' . $newTag['payType']);
            }
        }

        if (! empty($newCurrencies)) {
            $this->io->writeln('Currencies:');
            foreach ($newCurrencies as $newCurrency) {
                $this->io->writeln('title=' . $newCurrency['title'] . ', short=' . $newCurrency['short']);
            }
        }

        if (! empty($newAccounts)) {
            $this->io->writeln('Account:');
            foreach ($newAccounts as $newAccount) {
                $this->io->writeln('title=' . $newAccount['title']);
            }
        }

        $approveToCreate = $this->io->confirm('Would you like to create entities?', false);

        if ($approveToCreate) {
            $this->createDictionaries($newEntities);
        }
    }

    /**
     *
     * @param array $newEntities
     */
    protected function createDictionaries(array $newEntities): void
    {

        // TODO need to assert anv validate
        [
            $newTags,
            $newCurrencies,
            $newAccounts
        ] = $newEntities;

        $this->em->beginTransaction();

        if (! empty($newTags)) {
            foreach ($newTags as $newTag) {
                $tag = new Tag();
                $tag->setTitle($newTag['title']);
                $tag->setPayType($newTag['payType']);
                $tag->setUserId($this->user);

                $this->em->persist($tag);
            }
        }

        if (! empty($newCurrencies)) {
            foreach ($newCurrencies as $newCurrency) {
                $currency = new Currency();
                $currency->setTitle($newCurrency['title']);
                $currency->setShort($newCurrency['short']);
                // TODO валюта не завязана на пользователя. Это ок?
                // $currency->setUserId($this->user);

                $this->em->persist($currency);
            }
        }

        if (! empty($newAccounts)) {
            foreach ($newAccounts as $newAccount) {
                $account = new Account();
                $account->setTitle($newAccount['title']);
                $account->setUserId($this->user);

                $this->em->persist($account);
            }
        }

        $this->em->flush();
        $this->em->commit();
        $this->io->success('Dictionaries were created');
    }

    /**
     *
     * @return string
     */
    private function getCommandHelp(): string
    {
        $helpString = "
The <info>%command.name%</info> command validate dictionarias and load operations in the database.
csv-file format is:
+----+-----------+---------------------------+---------------------+----------+----------+----------+-----------+--------+----------+---------------+
| id | title     | notice                    | date                | type     | tag      | pay_type | account   | amount | currency | short_currency |
+----+-----------+---------------------------+---------------------+----------+----------+----------+-----------+--------+----------+---------------+
| 1  | lunch     |                           | 2014-08-14 12:49:00 | pay      | food     | 2        | bank card | -16    | euro     | €             |
| 2  | cake      |                           | 2014-08-14 19:27:00 | pay      | food     | 2        | bank card | -10    | euro     | €             |
| 3  | Exchange  |                           | 2014-08-15 09:30:00 | transfer |          |          | bank card | 300    | euro     | €             |
| 3  | Exchange  |                           | 2014-08-15 09:30:00 | transfer |          |          | cash      | -400   | dollar   | $             |
| 4  | perfumery | present to happy birthday | 2017-12-25 16:00:00 | pay      | presents | 2        | cash      | -20    | euro     | €             |
+----+-----------+---------------------------+---------------------+----------+----------+----------+-----------+--------+----------+---------------+
";

        return $helpString;
    }

    /**
     * 
     * @param string $path
     * @throws RuntimeException
     * @return string
     */
    static function guessDelimiter(string $path): string
    {
        $text = file_get_contents($path);

        $posibleDelimiters = [
            substr_count($text, "\t") => "\t",
            substr_count($text, ",") => ",",
            substr_count($text, ";") => ";"
        ];

        $maxCount = max(array_keys($posibleDelimiters));
        if ($maxCount < 10) {
            throw new RuntimeException("A fiew lines, please provide delimiter manually");
        }

        $delimiter = $posibleDelimiters[$maxCount];

        return $delimiter;
    }

    /**
     *
     * @param array $data
     * @return array
     */
    static function parseRow(array $data): array
    {
        $tag = [
            'title' => $data[5],
            'payType' => $data[6]
        ];
        $currency = [
            'title' => $data[9],
            'short' => $data[10]
        ];
        $account = [
            'title' => $data[7]
        ];
        $operation = [
            'id' => $data[0],
            'date' => new \DateTime($data[3]),
            'title' => $data[1],
            'notice' => self::toNullIfNeeded($data[2]),
            'type' => $data[4]
        ];
        $payment = [
            'amount' => self::toAmmount($data[8])
        ];

        return [
            $tag,
            $currency,
            $account,
            $operation,
            $payment
        ];
    }

    /**
     * 
     * @param string $path
     * @throws RuntimeException
     * @return int
     */
    static function getLinesCount(string $path): int
    {
        $count = 0;

        $fileHandler = fopen($path, "r");
        if (! $fileHandler) {
            throw new RuntimeException("Can't open file");
        }

        while (! feof($fileHandler)) {
            $count += substr_count(fread($fileHandler, 8192), "\n");
        }

        fclose($fileHandler);
        return $count;
    }

    /**
     *
     * @param string $ammountString
     * @return float
     */
    static function toAmmount(string $ammountString): float
    {
        return (double) preg_replace("/[^0-9+\-\.\,]/", "", $ammountString);
    }

    /**
     *
     * @param string $value
     * @return string|NULL
     */
    static function toNullIfNeeded(string $value): ?string
    {
        return ($value === 'NULL' || $value === '') ? null : $value;
    }
}

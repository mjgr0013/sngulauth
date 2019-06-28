<?php

namespace Sngular\Auth\Provider\Keycloak\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConnectAuthStartCommand extends Command
{
    /**
     * @var string
     */
    private $resourcesDir;

    /**
     * @var string
     */
    protected static $defaultName = 'sngular:auth:connect';

    protected function configure()
    {
        $this->resourcesDir = getcwd() . '/config';

        $this
            ->setDescription('Start the Connect configuration.')
            ->setHelp('This command build the necessary configuration for Keycloak - Connect');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Welcome to the Keycloak - Connect auth helper, We will ask you for some parameters');

        $questions = [
            [
                'name' => 'authServerUrl',
                'title' => 'Auth server url (Ej: http://localhost:8181/auth)',
                'default' => null
            ], [
                'name' => 'realm',
                'title' => 'Keycloak realm name',
                'default' => null
            ], [
                'name' => 'clientId',
                'title' => 'Client ID',
                'default' => null
            ], [
                'name' => 'clientSecret',
                'title' => 'Client secret',
                'default' => null
            ], [
                'name' => 'redirectUri',
                'title' => 'Url to be redirected after successful login',
                'default' => null
            ], [
                'name' => 'encryptionAlgorithm',
                'title' => 'Encryption algorithm to decode token info. Default: RS256',
                'default' => 'RS256'
            ], [
                'name' => 'encryptionKey',
                'title' => 'Encryption key to decode token info (WITHOUT BEGIN AND END)',
                'default' => null
            ],
        ];

        foreach ($questions as $question) {
            $questionObject = new Question(
                $question['title'],
                (null == $question['default']) ? $question['default'] : false
            );

            $result = $io->askQuestion($questionObject);

            if (!$result && !$question['default']) {
                $io->warning('Parameter ' . $question['name'] . ' is mandatory (there is no default value)');
                return;
            }

            $config[$question['name']] = $result;
        }

        $writeResult = $this->exportConfigToFIle($config);

        if (false == $writeResult) {
            $io->error('File cannot be placed at: ' . $this->resourcesDir);
            return;
        }

        $io->success('Connect file config has been placed here: ' . $this->resourcesDir . "/connect_config.php");
    }

    /**
     * @param array $config
     * @return bool|int
     */
    protected function exportConfigToFIle(array $config)
    {
        $var_str = $this->varexport($config, true);
        $var     = "<?php\n\n\$config = $var_str;";
        return file_put_contents($this->resourcesDir . "/connect_config.php", $var);
    }

    /**
     * Useful method for
     * @author https://gist.github.com/stemar
     * @param $expression
     * @param bool $return
     * @return mixed|string|string[]|null
     */
    protected function varexport($expression, $return = FALSE)
    {
        $export = var_export($expression, TRUE);
        $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
        $array  = preg_split("/\r\n|\n|\r/", $export);
        $array  = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [NULL, ']$1', ' => ['], $array);
        $export = join(PHP_EOL, array_filter(["["] + $array));

        if ((bool)$return)
            return $export;
        else
            return $export;
    }
}
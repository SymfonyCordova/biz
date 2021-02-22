<?php


namespace Zler\Biz\Laravel\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Zler\Biz\Laravel\Common\StringToolkit;

class ScaffoldCommand extends Command
{
    private $mode;
    private $names;
    private $paths;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'biz:scaffold {tableName} {moduleName} {mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'biz创建Dao|Service脚手架';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('<info>创建脚手架</info>');
        $this->initInputs();
        $this->initPaths();

        if (strpos($this->mode, 'D') !== false) {
            $this->createDaoTemplate();
            $this->info('<info>Dao创建完成</info>');
        }

        if (strpos($this->mode, 'S') !== false) {
            $this->createServiceTemplate();
            $this->info('<info>Service创建完成</info>');
        }

//        if (strpos($this->mode, 'C') !== false) {
//            $this->createControllerTemplate();
//            $this->info('<info>Controller创建完成</info>');
//        }
    }

    protected function initInputs()
    {
        $this->mode = $this->argument('mode');

        $this->tableName = $this->argument('tableName');
        $this->moduleName = $this->argument('moduleName');
        //$this->info(sprintf(" tableName:%s, moduleName:%s, mode:%s,\n",  $this->tableName, $this->moduleName, $this->mode));

        $smallName = StringToolkit::toCamelCase($this->tableName); //userToken
        $bigName = ucfirst($smallName); //UserToken


        $smallPluralName = $this->simplePluralize($smallName); //userTokens
        $bigPluralName = $this->simplePluralize($bigName); //UserTokens

        $underscoreName = StringToolkit::toUnderScore($smallName); //user_token
        $underscorePluralName = $this->simplePluralize($underscoreName); //user_tokens

        $dashCaseName = $this->underscoreNameToDashCase($underscoreName); //user-token

        //table fields
        $tableInfo = DB::select("desc `{$this->tableName}`");
        $daoConditionFields = '';
        $timestampFields = '';
        $tableFields = '';
        $tableFieldsWithDefaultValue = '';
        foreach ($tableInfo as $fieldInfo) {
            $fieldInfo = (array)$fieldInfo;
            if ($fieldInfo['Field'] == 'id') {
                continue;
            }

            $daoConditionFields .= "'{$fieldInfo['Field']} = :{$fieldInfo['Field']}', \n";


            if (in_array($fieldInfo['Field'], array('created_time', 'updated_time'))) {
                $timestampFields .= "'{$fieldInfo['Field']}', \n";
                continue;
            }

            $tableFields .= "'{$fieldInfo['Field']}', \n";

            if (strpos($fieldInfo['Type'], 'int') === 0) {
                $defaultValue = empty($fieldInfo['Default']) ? 0 : $fieldInfo['Default'];
            } else {
                $defaultValue = empty($fieldInfo['Default']) ? "''" : "'{$fieldInfo['Default']}'";
            }
            $tableFieldsWithDefaultValue .= "'{$fieldInfo['Field']}' => {$defaultValue}, \n";
        }

        $this->names = array(
            'phpStart' =>'<?php',
            'tableName' => $this->tableName,
            'moduleName' => $this->moduleName,
            'smallName' => $smallName,
            'bigName' => $bigName,
            'smallPluralName' => $smallPluralName,
            'underscorePluralName' => $underscorePluralName,
            'bigPluralName' => $bigPluralName,
            'dashCaseName' => $dashCaseName,
            'underscoreName' => $underscoreName,
            'daoConditionFields' => $daoConditionFields,
            'tableFields' => $tableFields,
            'tableFieldsWithDefaultValue' => $tableFieldsWithDefaultValue,
            'timestampFields' => $timestampFields,
        );
    }

    protected function initPaths()
    {
        //$rootDirectory = preg_replace("/\\\/","/", app_path());
        $rootDirectory = base_path('biz');

        $tpl = __DIR__.'/Template';
        $dao = $rootDirectory.'/'.$this->names['moduleName'].'/Dao';
        $service = $rootDirectory.'/'.$this->names['moduleName'].'/Service';
        //$testService = $rootDirectory.'/../tests/'.$this->names['moduleName'].'/Service';
        //$controller = $rootDirectory.'/Http/Controllers/Api';

        $this->paths = array(
            'tpl'           => $tpl,
            'dao'           => $dao,
            'dao_impl'      => $dao.'/Impl',
            'Service'       => $service,
            'service_impl'  => $service.'/Impl',
            //'test_service'  => $testService,
            //'controller'    => $controller,
        );
    }

    protected function createDaoTemplate()
    {
        $filesystem = new Filesystem();

        $daoContent = $this->blade2str(file_get_contents(sprintf('%s/Dao.blade.php', $this->paths['tpl'])), $this->names);
        $serviceContent = $this->blade2str(file_get_contents(sprintf('%s/DaoImpl.blade.php', $this->paths['tpl'])), $this->names);

        $filesystem->dumpFile("{$this->paths['dao']}/{$this->names['bigName']}Dao.php", $daoContent, 0777);
        $filesystem->dumpFile("{$this->paths['dao_impl']}/{$this->names['bigName']}DaoImpl.php", $serviceContent, 0777);
    }

    protected function createServiceTemplate()
    {
        $filesystem = new Filesystem();

        $serviceContent = $this->blade2str(file_get_contents(sprintf('%s/Service.blade.php', $this->paths['tpl'])), $this->names);
        $serviceImplContent = $this->blade2str(file_get_contents(sprintf('%s/ServiceImpl.blade.php', $this->paths['tpl'])), $this->names);
        //$testContent = $this->blade2str(file_get_contents(sprintf('%s/ServiceTest.blade.php', $this->paths['tpl'])), $this->names);

        $filesystem->dumpFile("{$this->paths['Service']}/{$this->names['bigName']}Service.php", $serviceContent, 0777);
        $filesystem->dumpFile("{$this->paths['service_impl']}/{$this->names['bigName']}ServiceImpl.php", $serviceImplContent, 0777);
        //$filesystem->dumpFile("{$this->paths['test_service']}/{$this->names['bigName']}ServiceTest.php", $testContent, 0777);
    }

    protected function createControllerTemplate()
    {
        $filesystem = new Filesystem();

        $content = $this->blade2str(file_get_contents(sprintf('%s/AdminController.blade.php', $this->paths['tpl'])), $this->names);

        $filesystem->dumpFile("{$this->paths['controller']}/{$this->names['bigName']}Controller.php", $content, 0777);
    }

    protected function blade2str($blade,$data = array())
    {
        $data['__env'] = app(ViewFactory::class);
        $str = Blade::compileString($blade);

        ob_start() and extract($data, EXTR_SKIP);
        try {
            eval('?>'.$str);
        }
        catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

    protected function simplePluralize($singular)
    {
        $lastLetter = strtolower($singular[strlen($singular) - 1]);
        $lastLetter2 = strtolower($singular[strlen($singular) - 2]);

        if (in_array($lastLetter, array('s', 'x')) || in_array($lastLetter.$lastLetter2, array('sh', 'es'))) {
            return $singular.'es';
        } elseif ($lastLetter == 'y' && in_array($lastLetter2, array('a', 'e', 'i', 'o', 'u'))) {
            return substr($singular, 0, -1).'ies';
        } else {
            return $singular.'s';
        }
    }

    protected function underscoreNameToDashCase($underscoreName)
    {
        return str_replace('_', '-', $underscoreName);
    }
}

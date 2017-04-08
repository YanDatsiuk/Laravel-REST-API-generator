<?php

namespace TMPHP\RestApiGenerators\Core;

/**
 * Class StubCompiler
 * @package packages\RestApiGenerators\Core
 */
abstract class StubCompiler
{
    /**
     * @var string Stub file for compilation
     */
    protected $stub;

    /**
     * @var string file name of the stub
     */
    protected $stubFileName;

    /**
     * @var string path, where will be saved compiled stub
     */
    protected $saveToPath;

    /**
     * @var string file name, which with will be saved compiled stub
     */
    protected $saveFileName;

    /**
     * StubCompiler constructor.
     *
     * @param string $saveToPath path, where will be saved compiled stub
     * @param string $saveFileName file name, which with will be saved compiled stub
     * @param string|null $stub Stub file for compilation
     */
    public function __construct(string $saveToPath, string $saveFileName, string $stub = null)
    {
        //path handling
        $this->saveToPath = $saveToPath;
        $this->saveFileName = $saveFileName;
        $this->createRequiredDirectories();

        //loading stub file
        if ($stub === null) {

            $this->stubFileName = str_replace('Compiler', '', (new \ReflectionClass($this))->getShortName());
            $this->stubFileName .= '.stub';

            $this->stub = file_get_contents($this->getClassDirectory() . '/stubs/' . $this->stubFileName);
        }else{
            $this->stub = $stub;
        }
    }

    /**
     * Compile stub file with params
     *
     * @param array $params
     * @return string
     */
    abstract public function compile(array $params):string ;

    /**
     * Return directory of calling class
     *
     * @return string
     */
    public function getClassDirectory():string{

        return dirname((new \ReflectionClass($this))->getFileName());
    }

    /**
     * Save generated stub into the file
     *
     * @return mixed
     */
    public function saveStub(){

        //save stub in the folder
        file_put_contents(
            $this->saveToPath. $this->saveFileName,
            $this->stub
        );
    }

    /**
     * Create required directories for generated files
     */
    private function createRequiredDirectories()
    {
        if (!file_exists($this->saveToPath)) {
            mkdir($this->saveToPath, 0777, true);
        }
    }

}
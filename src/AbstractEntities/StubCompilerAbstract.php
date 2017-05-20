<?php

namespace TMPHP\RestApiGenerators\AbstractEntities;

/**
 * Class StubCompiler
 * @package packages\RestApiGenerators\Core
 */
abstract class StubCompilerAbstract
{
    /** @var string Stub file for compilation */
    protected $stub;

    /** @var string file name of the stub */
    protected $stubFileName;

    /** @var string path, where will be saved compiled stub */
    protected $saveToPath;

    /** @var string file name, which with will be saved compiled stub */
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
        $this->saveToPath = $saveToPath;
        $this->saveFileName = $saveFileName;
        $this->createRequiredDirectories();

        //loading stub file
        if ($stub === null) {
            $this->stubFileName = str_replace('Compiler', '', (new \ReflectionClass($this))->getShortName());
            $this->stubFileName .= '.stub';
            $this->stub = file_get_contents($this->getClassDirectory() . '/stubs/' . $this->stubFileName);
        } else {
            $this->stub = $stub;
        }
    }

    /**
     * Create required directories for generated files
     */
    private function createRequiredDirectories()
    {
        if (!file_exists($this->saveToPath)) {
            mkdir($this->saveToPath, 0755, true);
        }
    }

    /**
     * Return directory of calling class
     *
     * @return string
     */
    public function getClassDirectory(): string
    {
        return dirname((new \ReflectionClass($this))->getFileName());
    }

    /**
     * Compile stub file with params
     *
     * @param array $params
     *
     * @return string
     */
    abstract public function compile(array $params): string;

    /**
     * Save generated stub into the file
     *
     * @return $this
     */
    public function saveStub()
    {
        //save stub in the folder
        file_put_contents(
            $this->saveToPath . $this->saveFileName,
            $this->stub
        );

        return $this;
    }

    /**
     * Search and replace strings in stub file.
     *
     * @param array $searchAndReplacements keys are values being searched for.
     * And the values of the array are used to be replacements
     * @return $this
     */
    public function replaceInStub($searchAndReplacements = [])
    {
        //do replacements in stub
        foreach ($searchAndReplacements as $key => $value) {
            $this->stub = str_replace(
                $key,
                $value,
                $this->stub
            );
        }

        return $this;
    }

    /**
     * Get generated stub file.
     * If there is no saved file - get default stub.
     *
     * @return bool|string
     */
    public function getSavedStub()
    {
        if (file_exists($this->saveToPath. $this->saveFileName)){
            $savedStub = file_get_contents($this->saveToPath. $this->saveFileName);
        }else{

            //load default stub
            $savedStub = file_get_contents($this->getClassDirectory() . '/stubs/' . $this->stubFileName);

            //save default stub
            file_put_contents($this->saveToPath. $this->saveFileName, $savedStub);
        }

        return $savedStub;
    }

    /**
     * Stub file setter.
     *
     * @param string $stub
     * @return $this
     */
    public function setStub(string $stub)
    {
        $this->stub = $stub;

        return $this;
    }

    /**
     * Appending $stub string to the existing stub
     *
     * @param string $stub
     * @return $this
     */
    public function appendToStub(string $stub)
    {
        $this->stub .= "\n" . $stub;

        return $this;
    }
}
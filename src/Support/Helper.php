<?php

namespace TMPHP\RestApiGenerators\Support;

/**
 * Class Helper
 * @package TMPHP\RestApiGenerators\Support
 */
class Helper
{
    /**
     * Get model name from table name
     *
     * @param string $tableName
     * @param string $tablePrefix
     * @return string
     */
    public static function tableNameToModelName(string $tableName, string $tablePrefix = ''): string
    {
        //remove prefix from table
        if (starts_with($tableName, $tablePrefix)) {
            $tableName = str_replace_first($tablePrefix, '', $tableName);
        }

        return self::singularKebabCase($tableName);
    }

    /**
     * Get model names from table names
     *
     * @param array $tableNames
     * @param string $tablePrefix
     * @return array
     */
    public static function getModelNamesFromTableNames(array $tableNames, string $tablePrefix = 'tb_'): array
    {
        $modelNames = [];

        foreach ($tableNames as $tableName) {

            array_push($modelNames, self::tableNameToModelName($tableName, $tablePrefix));
        }

        return $modelNames;
    }

    /**
     * Convert string from kebab case, or snake case to singular kebab case
     *
     * @param string $string example: users_roles
     *
     * @return string example: user-role
     */
    public static function singularKebabCase(string $string): string
    {

        $delimiter = '_';

        if (str_contains($string, '-')) {
            $delimiter = '-';
        }

        $subStrings = explode($delimiter, $string);

        $result = '';

        foreach ($subStrings as $subString) {
            $result .= str_singular($subString);
            $result .= '-';
        }

        if (ends_with($result, '-')) {
            $result = substr($result, 0, strlen($result) - 1);
        }

        return $result;
    }

    /**
     * Pluralize string from kebab case.
     *
     * @param string $string example: user-role
     * @return string
     */
    public static function pluralizeKebabCase(string $string): string
    {
        //
        $subStrings = explode('-', $string);

        //
        $pluralizedSubStrings = [];
        foreach ($subStrings as $subString) {
            array_push($pluralizedSubStrings, str_plural($subString));
        }

        //
        $result = implode('-', $pluralizedSubStrings);

        return $result;
    }

    /**
     * Convert string in kebab notation to camelCase notation
     *
     * @param string $string
     *
     * @return string
     */
    public static function kebabToCamelCase(string $string): string
    {
        $_modelInCamelCaseNotation = '';

        foreach (explode('-', $string) as $kebabStringPart) {
            $_modelInCamelCaseNotation .= ucfirst($kebabStringPart);
        }

        return $_modelInCamelCaseNotation;
    }

    /**
     * Get name for the "belongTo" relation based on column name
     *
     * @param string $columnName
     * @param string $columnPostfix
     * @return string
     */
    public static function columnNameToBelongToRelationName(string $columnName, string $columnPostfix = '_id'): string
    {
        //remove prefix from column name
        if (ends_with($columnName, $columnPostfix)) {
            $columnName = str_replace_last($columnPostfix, '', $columnName);
        }

        return camel_case($columnName);
    }

    /**
     * Excluding protocol part from URL
     *
     * @param string $url
     * @return string'
     */
    public static function trimProtocolFromUrl(string $url): string
    {
        $parts = explode('://', $url);
        return (count($parts) > 1) ? $parts[1] : $parts[0];
    }

    /**
     * Check whether REST API project was generated.
     *
     * @return bool
     */
    public static function isRestProjectGenerated()
    {
        $modelPath = base_path(config('rest-api-generator.paths.models'));

        return file_exists($modelPath);
    }

    /**
     * Append a code to method's beginning.
     *
     * @param string $fileContent
     * @param string $codeToAppend
     * @param string $methodPayload
     * @return string
     */
    public static function appendCodeToMethod(string $fileContent, string $codeToAppend, string $methodPayload): string
    {
        $methodStartIndex = strpos($fileContent, $methodPayload);

        $firstCurlyBracketIndex = strpos($fileContent, '{', $methodStartIndex);

        $newFileContent = substr_replace($fileContent, $codeToAppend, $firstCurlyBracketIndex + 1, 0);

        return $newFileContent;
    }
}
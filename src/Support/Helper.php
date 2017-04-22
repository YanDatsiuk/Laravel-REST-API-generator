<?php

namespace TMPHP\RestApiGenerators\Support;

class Helper
{
    /**
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
}
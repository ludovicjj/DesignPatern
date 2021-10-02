<?php


namespace App\DependencyInjection;

use ReflectionNamedType;
use ReflectionParameter;

class Validator
{
    /** @var string $class */
    private $class;

    /** @var string $method */
    private $method;

    private const ALLOWED_TYPE = [
        "string" => "string",
        "int" => "integer",
        "array" => "array",
        "bool" => "boolean",
        "float" => "double",
        "double" => "double",
        "callable" => "Closure",
        "NULL" => "NULL"
    ];

    public function __construct(string $class)
    {
        $this->class = $class;
        $this->method = "__construct()";
    }

    public function validConstructorParameters(
        array $expectedParameter,
        array $givenParameter
    ): ParameterErrorList
    {
        $expectedType = $this->getExpectedConstructorParameterType($expectedParameter);
        $givenType = $this->getGivenParametersType($givenParameter, $expectedType);
        $errors = $this->getErrors($expectedType, $givenType);

        return new ParameterErrorList($errors);
    }

    /**
     * Get Type for each reflection parameter from reflection method
     *
     * @param array $reflectionParameters
     * @return array<string, string>
     */
    private function getExpectedConstructorParameterType(array $reflectionParameters): array
    {
        return array_reduce($reflectionParameters, function($accumulator, ReflectionParameter $reflectionParameter) {
            $reflectionType = $reflectionParameter->getType();
            $paramName = $reflectionParameter->getName();

            if ($reflectionType instanceof ReflectionNamedType) {
                $accumulator[$paramName] = $reflectionType->getName();
            }

            if ($reflectionType instanceof ReflectionNamedType && $reflectionParameter->isOptional()) {
                if ($reflectionParameter->isDefaultValueAvailable()) {
                    $type = gettype($reflectionParameter->getDefaultValue());
                    $accumulator[$paramName] = "{$reflectionType->getName()}|{$type}";
                } else {
                    $accumulator[$paramName] = "{$reflectionType->getName()}";
                }
            }

            if ($reflectionType === null) {
                $accumulator[$paramName] = "mixed";
            }

            return $accumulator;
        }, []);
    }

    private function getGivenParametersType(array $givenParameters, array $expectedType): array
    {
        return array_map(function ($givenParameter, $expectedType) {
            $type = gettype($givenParameter);
            if ($type === "object") {
                $class = get_class($givenParameter);
                if (in_array($expectedType, class_implements($class))) {
                    return $expectedType;
                }
                return $class;
            }
            return $type;
        }, $givenParameters, $expectedType);
    }

    private function getErrors(array $expectedType, array $givenType): array
    {
        $invalidParameters = array_map(function ($expected, $given, $parameterName) {
            if ($expected === "mixed") {
                return null;
            }

            if (preg_match("#^$expected$#", $given)) {
                return null;
            }

            if ($expected === $given) {
                return null;
            }

            if (array_key_exists($expected, self::ALLOWED_TYPE)) {
                if (self::ALLOWED_TYPE[$expected] === $given) {
                    return null;
                }
            }

            return new ParameterError($this->class, $this->method, $parameterName, $expected, $given);

        },$expectedType, $givenType, array_keys($expectedType));

        return array_filter($invalidParameters, function($value){
            return $value !== null;
        });
    }
}
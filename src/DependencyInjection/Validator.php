<?php


namespace App\DependencyInjection;

use ReflectionNamedType;
use ReflectionParameter;

class Validator
{
    /** @var string $class */
    private $class;

    private const ALLOWED_TYPE = [
        "int" => "integer",
        "integer" => "integer",
        "array" => "array",
        "float" => "float",
        "string" => "string",
        "callable" => "Closure",
        "boolean" => "bool",
        "double" => "double",
        "NULL" => "NULL"
    ];

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function validConstructorParameters(
        array $expectedParameter,
        array $givenParameter,
        string $method = "__construct()"
    ): ParameterErrorList
    {
        $expectedType = $this->getExpectedConstructorParameterType($expectedParameter);
        $givenType = $this->getGivenParametersType($givenParameter, $expectedType);
        $errors = $this->getErrors($expectedType, $givenType, $method);

        return new ParameterErrorList($errors);
    }

    private function getExpectedConstructorParameterType(array $reflectionParameters)
    {
        $expectedType = [];

        /** @var ReflectionParameter $reflectionParameter */
        foreach ($reflectionParameters as $reflectionParameter) {
            if ($reflectionParameter->isOptional()) {
                if ($reflectionParameter->isDefaultValueAvailable()) {
                    $expectedType[$reflectionParameter->getName()] = gettype($reflectionParameter->getDefaultValue());
                } else {
                    $expectedType[$reflectionParameter->getName()] = "unknown type";
                }
            } else {
                $reflectionType = $reflectionParameter->getType();
                if ($reflectionType instanceof ReflectionNamedType) {
                    $expectedType[$reflectionParameter->getName()] = $reflectionType->getName();
                } else {
                    $expectedType[$reflectionParameter->getName()] = $reflectionType;
                }
            }
        }
        return $expectedType;
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

    private function getErrors(array $expectedType, array $givenType, string $method): array
    {
        $invalidParameters = array_map(function ($expected, $given, $parameterName) use ($method) {
            if ($expected !== null) {
                if ($expected !== $given) {
                    if (array_key_exists($given, self::ALLOWED_TYPE)) {
                        if (self::ALLOWED_TYPE[$expected] === $given) {
                            return null;
                        }
                    }
                    return new ParameterError($this->class, $method, $parameterName, $expected, $given);
                }
            }
            return null;
        },$expectedType, $givenType, array_keys($expectedType));

        return array_filter($invalidParameters, function($value){
            return $value !== null;
        });
    }
}
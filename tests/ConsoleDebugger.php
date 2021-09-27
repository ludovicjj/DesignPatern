<?php


namespace Tests;


trait ConsoleDebugger
{
    private $textColors = [
        "danger"    => "31",
        "success"   => "32",
        "warning"   => "33",
        "default"   => "37"
    ];

    private $backgroundColors = [
        "danger"    => "41",
        "success"   => "42",
        "warning"   => "43",
        "default"   => "40"
    ];

    public function debugger(string $message, $textColor = null, $backgroundColor = null)
    {
        $output = "";

        if (!is_null($textColor)) {
            $textColor = $this->textColors[$textColor] ?? $this->textColors["default"];
            $output .= "\e[" . $textColor . "m";
        }

        if (!is_null($backgroundColor)) {
            $backgroundColor = $this->backgroundColors[$backgroundColor] ?? $this->backgroundColors["default"];
            $output .= "\e[" . $backgroundColor . "m";
        }

        $output .= $message . "\e[0m\n";
        file_put_contents("php://stdout", $output);
    }
}
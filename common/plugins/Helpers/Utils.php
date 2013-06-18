<?php namespace Helpers;
// simple utility functions
class Utils
{
  public static function is_array_empty($InputVariable)
  {
    $Result = true;

    if (is_array($InputVariable) && count($InputVariable) > 0)
    {
      foreach ($InputVariable as $Value)
      {
        $Result = $Result && is_array_empty($Value);
      }
    }
    else
    {
      $Result = empty($InputVariable);
    }

    return $Result;
  }
}

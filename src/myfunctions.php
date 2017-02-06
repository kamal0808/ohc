<?php
include_once 'index.php';
function solveExp($exp)
            {

            }

            function assignValue($var,$value)
            {
                $i=0;
                $flag=0;
                foreach($int as $variables)
                {
                    //echo "$delimiteq[0]<br/>";
                    //echo "$variables<br/>";
                    if(strnatcmp($variables,$var)==0)
                    {
                        $intval[$i]=$value;
                        $flag=1;
                        break;
                    }
                    $i++;
                }
                if($flag==1)
                    echo "found variable $int[$i] and updated with value $intval[$i]<br/>";
                else
                    echo "variable $var not declared. Please declare<br/>";
            }
?>

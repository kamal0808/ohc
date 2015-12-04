<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="bootstrap.min.css"/>
    <link rel="stylesheet" href="mycss.css"/>
    <script type="text/javascript" src="myscript.js">
    </script>
    <script type="text/javascript" src="bootstrap.min.js">
    </script>
    <script type="text/javascript" src="jquery.min.js">
    </script>
    <script type="text/javascript" src="https://www.google.com/jsapi">
    </script>
    <script type="text/javascript">
         // Load the Google Transliterate API
         $(function(){
    $(".instructions").click(function(){
        $(".instructions-area").toggle("fast");
    })
})


    google.load("elements", "1", {
            packages: "transliteration"
          });

      function onLoad() {
        var options = {
            sourceLanguage:
                google.elements.transliteration.LanguageCode.ENGLISH,
            destinationLanguage:
                [google.elements.transliteration.LanguageCode.HINDI],
            shortcutKey: 'ctrl+g',
            transliterationEnabled: true
        };

        // Create an instance on TransliterationControl with the required
        // options.
        var control =
            new google.elements.transliteration.TransliterationControl(options);

        // Enable transliteration in the textbox with id
        // 'transliterateTextarea'.
        control.makeTransliteratable(['transliterateTextarea']);
        var ids=new Array();
         $(function(){
            $(".variables").each(function(){
                ids.push(this.id);
            })
        })
        control.makeTransliteratable(ids);
        
      }
      google.setOnLoadCallback(onLoad);
      
      
    </script>
    
  </head>
  <body>
      <?php
      $mycode=$output="";
        $int=array();
        $intval=array();
        $arr=0;
        $sourcecode="";
        $ifdone=0;
        $ifpresent=0;
        $uservardone=0;
        if(isset($_POST['mycode']))
        {
            compile($_POST['mycode'],";","whole");
        }
        function compare($expr1, $operator, $expr2) 
        { 
           switch(strtolower($operator)) 
           { 
              case '==': 
                 return $expr1 == $expr2; 
              case '>=': 
                 return $expr1 >= $expr2; 
              case '<=': 
                 return $expr1 <= $expr2; 
              case '!=': 
                 return $expr1 != $expr2; 
              case '&&': 
              case 'and': 
                 return $expr1 && $expr2; 
              case '||': 
              case 'or': 
                 return $expr1 || $expr2; 
              default: 
                 throw new Exception("Invalid operator '$operator'"); 
           } 
        }  
        function engDig($exp)
        {
            $exp=str_replace("१", "1", $exp);
            $exp=str_replace("२", "2", $exp);
            $exp=str_replace("३", "3", $exp);
            $exp=str_replace("४", "4", $exp);
            $exp=str_replace("५", "5", $exp);
            $exp=str_replace("६", "6", $exp);
            $exp=str_replace("७", "7", $exp);
            $exp=str_replace("८", "8", $exp);
            $exp=str_replace("९", "9", $exp);
            $exp=str_replace("०", "0", $exp);
            return $exp;
        }
        
        function evalExp($exp)
        {
            
//            echo "this is its converted expression $exp ";
            $exp=engDig($exp);
            $exp="return $exp;";
//            echo "and its value is ".eval($exp);
            return eval($exp);
        }
        
        function hindiDig($exp)
        {
            $exp=str_replace("1", "१", $exp);
            $exp=str_replace("2", "२", $exp);
            $exp=str_replace("3", "३", $exp);
            $exp=str_replace("4", "४", $exp);
            $exp=str_replace("5", "५", $exp);
            $exp=str_replace("6", "६", $exp);
            $exp=str_replace("7", "७", $exp);
            $exp=str_replace("8", "८", $exp);
            $exp=str_replace("9", "९", $exp);
            $exp=str_replace("0", "०", $exp);
//            echo "this is its converted expression $exp ";
//            echo "and its value is ";
            return $exp;
        }
        
            $loopcount;
        function looping($value)
        {
            global $loopcount;
//            echo "loop number $loopcount<br/>";
            $pos=stripos($value,"जबतक");
            $pos=$pos+strlen("जबतक");
            $exp=substr($value,$pos);
//            echo "Executing while statement $exp <br/>";
            $end=strpos($value,"{");
            $beg=$pos;
            $whilecondition=substr($value,$beg,$end-$beg);
//            echo "now while condition is $whilecondition<br/>";
            $whilecondition=evaluate($whilecondition); //returns 1 if true, else null
            $whilecondition=evalExp($whilecondition); //returns 1 if true, else null
//            echo "now again if condition is $whilecondition<br/>";
            if($whilecondition==1)
            {
                $beg=strpos($value, "{")+strlen("{");
                $end=strpos($value, "}");
                $whileblock=substr($value, $beg, $end-$beg);
//                echo "while block contains $whileblock<br/>";
                compile($whileblock,":","thread");
                $loopcount++;
                looping($value);
            }
        }
        
        function evaluate($output)
        {
            global $int;
            global $intval;
            $i=0;
            foreach($int as $var)   //replacing variable with its value in output
            {
                $hi="$".trim($var);
                if(strstr($output,$hi))
                {
//                                            echo "$hi<br/>";
//                                             echo "$intval[$i]<br/>";
//                                             echo "$output<br/>";
                    $output=str_replace($hi, $intval[$i], $output);
                                                $flag=1;    //found variable
//                    echo "$output<br/>";
                }
                $i++;
            }
            if(strstr($output,"$"))
            {
                $pos1=strpos($output,"$");
                $pos1=$pos1+strlen("$");
                $exp=substr($output,$pos1);
                $temp=explode(" ",$exp);
                $exp=$temp[0];
                $oexp=$exp;
                $anotherflag=0;
//                    echo "this is an expression $exp<br/>";
                    $exp=evalExp($exp);

//                    echo "its value is $exp<br/>";
                    $output=str_replace("$".$oexp, $exp, $output);

            }
            return $output;
        }
        
        
        function compile($code,$delimiter,$app)
        {
            global $mycode;
            global $output;
            global $int;
            global $intval;
            global $arr;
            global $sourcecode;
            global $ifdone;
            global $ifpresent;
            global $uservardone;
            $output="";
            $mytokens=array("लिखो","पूर्ण","=","निवेश","$","अगर","वरना","जबतक");
            $mynums=array("१","२","३","४","५","६","७","८","९","०");
            $mycode=$code;
            if(strcmp($app, "whole")==0)
            {
                $sourcecode=$code;
            }
            
            $temp=explode($delimiter, $mycode);
            
            foreach($temp as $value)
            {
                foreach($mytokens as $token)
                {
                    if(strstr($value, $token))
                    {
                        switch($token):
                            case "लिखो":    //to print output
                               // $flag=0;
                                
                                $pos=stripos($value,"लिखो");
                                
                                if($pos==2||$pos==0)
                                {
                                    $pos=$pos+strlen("लिखो");
                                    $output=substr($value,$pos); //output shown as shown in echo
                                    
                                    //                                $tempoutput=stripslashes(substr($value,$pos));
                                    $i=0;
                                    if(strstr($value,"$"))
                                    {
//                                        echo "idhar $value<br/>";
                                        $output=evaluate($output);
                                    }
                                    $output=hindiDig($output);
                                    echo<<<_END
                                            <script type="text/javascript">
                                                $(function(){
                                                $(".code-output-area").append("$output<br/>");
                                                })
                                             </script>
_END;
                                }
                                
                                break;
                            case "पूर्ण":     //variable declaration
                                if(!(strstr($value,"="))&&strpos($value, "पूर्ण")>=0)
                                {
                                    $pos=stripos($value,"पूर्ण");
                                    $pos=$pos+strlen("पूर्ण");
                                    $int[$arr]=substr($value,$pos);
                                    $intval[$arr]=0;
//                                    echo "New variable declared $int[$arr] with no value<br/>";
                                    $arr++;
                                }
                                        
                                break;
                                
                            case "=":
                                if(!(strstr($value,"अगर")||strstr($value,"जबतक")||strstr($value,"वरना")))
                                {
                                    
                                    $flag=0;
                                    $i=0;
                                    $delimiteq=explode("=",$value); //delimiting equalto sign
//                                    echo $delimiteq[0]." ".$delimiteq[1]."<br/>";
                                    if(strstr($delimiteq[1],"$"))    //likho doesnt contain any variables, check for expresions
                                    {
                                        $delimiteq[1]=evaluate($delimiteq[1]);
                                    }




                                    if(strstr($delimiteq[0],"पूर्ण"))
                                    {
                                        $delimitpoorn=explode("पूर्ण",$delimiteq[0]);   //delimiting पूर्ण
                                        foreach($int as $variables)
                                        {
                                            if($delimitpoorn[1]==$variables)    //check if variable already declared
                                            {
                                                $flag=1;
                                            }
                                        }
                                        if($flag==1)    //declaring a declared variable. generate error
                                        {
                                            echo "Variable already declared<br/>";
                                        }
                                        else
                                        {
                                            //new declaration

                                            $int[$arr]=$delimitpoorn[1];

                                            if(strlen($delimiteq[1])>0)
                                            {
                                                $intval[$arr]=$delimiteq[1];
//                                            echo "New variable declared $delimitpoorn[1] with value $delimiteq[1]<br/>";
                                                }

                                            else
                                            {

                                                $intval[$arr]=0;
//                                                echo "New variable declared $delimitpoorn[1] with no value <br/>";
                                            }   
                                            $arr++;
    //                                     
                                        }
                                    }
                                    else        // already declared variables updating value
                                    {

                                        $i=0;
                                        $flag=0;
                                        foreach($int as $variables)
                                        {
//                                            echo "$delimiteq[0]<br/>";
//                                            echo "$variables<br/>";
                                            if(strnatcmp($variables,$delimiteq[0])==0)
                                            {
                                                $intval[$i]=$delimiteq[1];
                                                $flag=1;
                                                break;
                                            }
                                            $i++;
                                        }
                                        if($flag==1)
                                        {}//echo "found variable $int[$i] and updated with value $intval[$i]<br/>";
                                        else
                                            echo "variable $delimiteq[0] not declared. Please declare<br/>";

                                    }

                                }
                                break;
                                
                                case "निवेश":
                                    $flag=0;
                                    $pos=stripos($value,"निवेश");
                                    $pos=$pos+strlen("निवेश");
                                    $input=substr($value,$pos);
//                                    echo "inputting into $input<br/>";
                                    foreach($int as $variables)
                                    {
                                        if($input==$variables)    //check if variable already declared
                                        {
                                            $flag=1;
                                        }
                                    }
                                    $input=trim($input);
                                    if($flag==0)
                                        echo "variable $input not declared<br/>";
                                    elseif(!isset($_POST[$input]))
                                    {
                                        echo<<<_END
                                        <script type="text/javascript">
                                            $(function(){
                                            $(".code-output-area").append("<input class='variables' id='$input' type='text' name='$input'/><br/>");
                                            })
                                         </script>
_END;
                                    }
                                    break;
                                    
                                    case "अगर":
                                        $pos=stripos($value,"अगर");
                                        $pos=$pos+strlen("अगर");
                                        $exp=substr($value,$pos);
//                                        echo "Executing if statement $exp <br/>";
                                        $end=strpos($value,"{");
                                        $beg=$pos;
                                        $ifcondition=substr($value,$beg,$end-$beg);
//                                        echo "now if condition is $ifcondition<br/>";
                                        $ifcondition=evaluate($ifcondition); //returns 1 if true, else null
                                        $ifcondition=evalExp($ifcondition); //returns 1 if true, else null
                                        $ifpresent=1;
//                                        echo "now again if condition is $ifcondition<br/>";
                                        if($ifcondition==1)
                                        {
                                            $beg=strpos($value, "{")+strlen("{");
                                            $end=strpos($value, "}");
                                            $ifblock=substr($value, $beg, $end-$beg);
//                                            echo "if block contains $ifblock<br/>";
                                            compile($ifblock,":","thread");
                                            $ifdone=1;
                                        }
                                        break;
                                        
                                    case "वरना":
                                        if($ifdone==0&&$ifpresent==1)
                                        {
                                            $beg=strpos($value, "{")+strlen("{");
                                            $end=strpos($value, "}");
                                            $elseblock=substr($value, $beg, $end-$beg);
//                                            echo "else block contains $elseblock<br/>";
                                            compile($elseblock,":","thread");
                                            $ifpresent=0;
                                            $ifdone=0;
                                        }
                                        else
                                            echo "no if statement defined<br/>";
                                        break;
                                     
                                    case "जबतक":
                                        looping($value);
                                        break;    
//                                    
                            
                        endswitch;
                    }
                    
                }
                    $count=count($_POST);
                    $count--;   //to eliminate $mycode from the counting list
                foreach($_POST as $key=>$inputs)
                {
                    if((strcmp($inputs, $mycode)!=0)&&$count!=0)
                    {
//                        echo "$inputs into $key<br/>";
                        $e=0;
                        foreach($int as $varname)
                        {
                 //           echo "going thru variable $varname and input variable $key<br/>";
                            if(strnatcmp($varname,$key)==0)
                            {
                                $uservardone=1;
                                $intval[$e]=engDig($inputs);
                   //             echo "updating value of variable $varname with user input $inputs<br/>";
                            }
                            $e++;
                        }
                        $count--;
                    }
                }
            }
//            $g=0;
//                echo "all variables with their values<br/>";
//            foreach($int as $variables)
//            {
//                echo "$variables has value $intval[$g]<br/>";
//                $g++;
//            }
        }
        
      ?>
      <div class="wrap">
          <div class="header">
              <div class="row">
                  <div class="notibar col-lg-12 col-md-12 col-sm-12 col-xs-12">
                      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                          <h1>Online Hindi Compiler</h1>
                      </div>
                  </div>
              </div>
          </div>
          <div class="midder">
              <div class="instructions">
                  <h2>Click me for Instructions</h2>
              </div>
              <div class="row instructions-area">
                      <h3>Instructions for Hindi Programming Language:</h3>
                  <div class="col-md-6 col-sm-6">
                      <pre>
KEYWORDS:

पूर्ण = variable
निवेश = user input
लिखो = print
अगर = if condition
वरना = else statement
जबतक = while loop


DECLARING VARIABLES:

पूर्ण क;

Here क  is a variable,
पूर्ण  is the keyword for defining it as a variable.
It is not necessary to provide the variable datatype, as it is a loosely written language.

INITIALIZING VARIABLES:

1) क=३४;
2) पूर्ण क=३४;

Variables can be initialized while declaration or at any time in the program.

ACCEPTING USER INPUT:

निवेश क;

Here, निवेश  is the keyword used for accepting user input in the variable क.
The variable क has to be declared previously to accept user input.

PRINTING TEXT:

लिखो मेरा नाम कमल है;

Here, the लिखो  keyword is used for printing text as it is. Anything or everything written after the word लिखो will be treated as text to be printed.

EXPRESSIONS:

Arithmetic :

Arithmetic expressions need to be preceded with a "$" sign in order for the compiler to evaluate them.

For example,

1)लिखो २+२;

This will give output as:

२+२ 

but

लिखो $२+२;
or
लिखो $(२+२);

will give the output as:

4

2) पूर्ण क=२+२;

This will initialize the variable क  with the string "२+२".

Whereas,

पूर्ण क=$२+२;
or
पूर्ण क=$(२+२);

will initialize the variable क  with the value 4.


                      </pre>
                  </div>
                  <div class="col-md-6 col-sm-6">
                      <pre>
USING VARIABLE VALUES:

The values of variables can be referred by preceding a "$" sign before the variable name.

For example,

1) पूर्ण ग=$क;

This will assign the value of variable क  to the variable ग.

Whereas, 

पूर्ण ग=क;

This will assign the value "क"  to the variable ग.


CONDITIONAL STATEMENTS:

1) IF condition

Syntax:

अगर(condition)
{

     do something:    //to end with colon(:) instead of semicolon(;)
};                             //to end with semicolon(;)

Example:

अगर($क>०)
{
लिखो क का मान शुन्य से बड़ा है:
क=$क*१०;
};


2) IF-ELSE condition

अगर(condition)
{
     do something:    
};                             
वरना
{
     do something else:   // to end with colon(:)
};       //to end with semicolon (;)

Example:

अगर($क>१०)
{
     लिखो क का मान १० से बड़ा है:
};                             
वरना
{
     लिखो क का मान १० से छोटा है:
};


LOOPING STATEMENTS:

It uses an alternative  of the original while loop, that checks a condition and performs a loop.

Syntax:

जबतक(condition)
{
     do something:    //end with colon(:)
};                             // end with semi-colon(;)


Example:

पूर्ण क=१०;
जबतक($क>०)
{
     लिखो $क:
     क=$($क-१);
};

The above example will give the following output:

१०
९
८
७
६
५
४
३
२
१
                  </pre>
                  </div>
              </div>
              <div class="row">
                      <form action="index.php" method="post">
                  <div class="col-md-6 col-sm-6 code-input">
                      <h3>Enter hindi code here:</h3>
                      <div class="col-md-12 col-sm-12 code-input-area">
                            <div class="form-group">
            <!--                    <label>Text area</label>-->
                                <textarea class="form-control" id="transliterateTextarea" name="mycode"><?php
                                echo $sourcecode;
                                
                                ?></textarea>
                            </div>
                             <button type="submit" class="btn btn-default">Compile Hindi Code</button> 

                      </div>
                  </div>
                  <div class="col-md-6 code-output col-sm-6">
                      <h3>Output:</h3>
                      <div class="col-md-12 code-output-area col-sm-12">
                          <?php
                          
                          //echo $output;
                          ?>
                      </div>
                  </div>
                  </form>
              </div>
          </div>
      </div>
  </body>
  
</html>
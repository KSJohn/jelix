<?php
/**
* @package     testapp
* @subpackage  jelix_tests module
* @author      Jouanneau Laurent
* @contributor
* @copyright   2007 Jouanneau laurent
* @link        http://www.jelix.org
* @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

require_once(JELIX_LIB_TPL_PATH.'jTplCompiler.class.php');

class testJtplCompiler extends jTplCompiler {

   public function setTrusted($trusted){
        $this->_trusted = $trusted;
   }

   public function testParseExpr($string, $allowed=array(), $exceptchar=array(';'), $splitArgIntoArray=false){
        return $this->_parseFinal($string, $allowed, $exceptchar, $splitArgIntoArray);
   }

   public function testParseVarExpr($string){
        return $this->_parseFinal($string,$this->_allowedInVar, $this->_excludedInVar);
   }

   public function testParseForeachExpr($string){
        return $this->_parseFinal($string,$this->_allowedInForeach, array(';','!'));
   }

   public function testParseAnyExpr($string){
        return $this->_parseFinal($string, $this->_allowedInExpr, array());
   }

   public function testParseAssignExpr($string){
        return $this->_parseFinal($string,$this->_allowedAssign);
   }

   public function testParseAssignExpr2($string){
        return $this->_parseFinal($string,$this->_allowedAssign, array(';'),true);
   }

   public function testParseVariable($string){
        return $this->_parseVariable($string);
   }

}




class UTjtplexpr extends jUnitTestCase {

    protected $varexpr = array(
        'a'=>'a',
        '"aaa"'=>'"aaa"',
        '123'=>'123',
        '  '=>'  ',
        '123.456'=>'123.456',
        '->'=>'->',
        "'aze'"=>"'aze'",
        '$aa'=>'$t->_vars[\'aa\']',
        '$aa.$bb'=>'$t->_vars[\'aa\'].$t->_vars[\'bb\']',
        '$aa."bbb"'=>'$t->_vars[\'aa\']."bbb"',
        '$aa+234'=>'$t->_vars[\'aa\']+234',
        '$aa-234'=>'$t->_vars[\'aa\']-234',
        '$aa*234'=>'$t->_vars[\'aa\']*234',
        '$aa/234'=>'$t->_vars[\'aa\']/234',
        '!$aa'=>'!$t->_vars[\'aa\']',
        'array($aa)'=>'array($t->_vars[\'aa\'])',
        ' array(   $aa  )   '=>' array(   $t->_vars[\'aa\']  )   ',
        ' array("e"=>$aa, "tt"=>987  )'=>' array("e"=>$t->_vars[\'aa\'], "tt"=>987  )',
        '@aa@'=>'jLocale::get(\'aa\')',
        '@aa.$ooo@'=>'jLocale::get(\'aa.\'.$t->_vars[\'ooo\'].\'\')',
        '@aa~bbb@'=>'jLocale::get(\'aa~bbb\')',
        '@aa~trc.$abcd.popo@'=>'jLocale::get(\'aa~trc.\'.$t->_vars[\'abcd\'].\'.popo\')',
        '@$aa~trc.$abcd.popo@'=>'jLocale::get(\'\'.$t->_vars[\'aa\'].\'~trc.\'.$t->_vars[\'abcd\'].\'.popo\')',
        '$aa.@trc.$abcd.popo@'=>'$t->_vars[\'aa\'].jLocale::get(\'trc.\'.$t->_vars[\'abcd\'].\'.popo\')',
        '@aa~trc.234.popo@'=>'jLocale::get(\'aa~trc.234.popo\')',
        '@aa~trc.23.4.popo@'=>'jLocale::get(\'aa~trc.23.4.popo\')',
        '$aa*count($bb)'=>'$t->_vars[\'aa\']*count($t->_vars[\'bb\'])',
        '$aa & $bb'=>'$t->_vars[\'aa\'] & $t->_vars[\'bb\']',
        '$aa | $bb'=>'$t->_vars[\'aa\'] | $t->_vars[\'bb\']',
        '$aa++'=>'$t->_vars[\'aa\']++',
        '$aa--'=>'$t->_vars[\'aa\']--',
        '$bb->bar'=>'$t->_vars[\'bb\']->bar',
        '@abstract.as.break.case.catch.class.clone@'=>'jLocale::get(\'abstract.as.break.case.catch.class.clone\')',
        '@const.continue.declare.default.do.echo.else.elseif.empty@'=>'jLocale::get(\'const.continue.declare.default.do.echo.else.elseif.empty\')',
        '@exit.final.for.foreach.function.global.if.implements.instanceof@'=>'jLocale::get(\'exit.final.for.foreach.function.global.if.implements.instanceof\')',
        '@interface.and.or.xor.new.private.public@'=>'jLocale::get(\'interface.and.or.xor.new.private.public\')',
        '@protected.return.static.switch.throw.try.use.var.while@'=>'jLocale::get(\'protected.return.static.switch.throw.try.use.var.while\')',
        '$aa*(234+$b)'=>'$t->_vars[\'aa\']*(234+$t->_vars[\'b\'])',
        '$aa[$bb[4]]'=>'$t->_vars[\'aa\'][$t->_vars[\'bb\'][4]]',
    );

    protected $varexprTrustedMode = array(
        '$aaa.PHP_VERSION'=>'$t->_vars[\'aaa\'].PHP_VERSION',
    );

    protected $varexprUnTrustedMode = array(

    );




    function testVarExprTrustedMode() {
        $compil = new testJtplCompiler();
        $compil->setTrusted(true);
        foreach($this->varexpr as $k=>$t){
            try{
                $res = $compil->testParseVarExpr($k);
                $this->assertEqualOrDiff($t, $res);
            }catch(jException $e){
                $this->fail("Test '$k', Unknown Jelix Exception: ".$e->getMessage().' ('.$e->getLocaleKey().')');
            }catch(Exception $e){
                $this->fail("Test '$k', Unknown Exception: ".$e->getMessage());
            }
        }
        foreach($this->varexprTrustedMode as $k=>$t){
            try{
                $res = $compil->testParseVarExpr($k);
                $this->assertEqualOrDiff($t, $res);
            }catch(jException $e){
                $this->fail("Test '$k', Unknown Jelix Exception : ".$e->getMessage().' ('.$e->getLocaleKey().')');
            }catch(Exception $e){
                $this->fail("Test '$k', Unknown Exception: ".$e->getMessage());
            }
        }
    }

    function testVarExprUnTrustedMode() {
        $compil = new testJtplCompiler();
        $compil->setTrusted(false);
        foreach($this->varexpr as $k=>$t){
            try{
                $res = $compil->testParseVarExpr($k);
                $this->assertEqualOrDiff($t, $res);
            }catch(jException $e){
                $this->fail("Test '$k', Unknown Jelix Exception : ".$e->getMessage().' ('.$e->getLocaleKey().')');
            }catch(Exception $e){
                $this->fail("Test '$k', Unknown Exception: ".$e->getMessage());
            }
        }
        foreach($this->varexprUnTrustedMode as $k=>$t){
            try{
                $res = $compil->testParseVarExpr($k);
                $this->assertEqualOrDiff($t, $res);
            }catch(jException $e){
                $this->fail("Test '$k', Unknown Jelix Exception : ".$e->getMessage().' ('.$e->getLocaleKey().')');
            }catch(Exception $e){
                $this->fail("Test '$k', Unknown Exception: ".$e->getMessage());
            }
        }
    }

    protected $badvarexpr = array(
        '$'=>array('jelix~errors.tpl.tag.character.invalid',array('','$','')),
        'foreach($a)'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','foreach','')),
        '@aaa.bbb'=>array('jelix~errors.tpl.tag.locale.end.missing',array('','')),
        '@aaa.b,bb@'=>array('jelix~errors.tpl.tag.character.invalid',array('',',','')),
        '@@'=>array('jelix~errors.tpl.tag.locale.invalid',array('','')),
        '[$aa/234]'=>array('jelix~errors.tpl.tag.character.invalid',array('','[','')),
        '($aa/234)'=>array('jelix~errors.tpl.tag.character.invalid',array('','(','')),
        '$b+($aa/234'=>array('jelix~errors.tpl.tag.bracket.error',array('','')),
        '$b+(($aa/234)'=>array('jelix~errors.tpl.tag.bracket.error',array('','')),
        '$aa/234)'=>array('jelix~errors.tpl.tag.bracket.error',array('','')),
        '$aa/234))'=>array('jelix~errors.tpl.tag.bracket.error',array('','')),
        '$aa[234'=>array('jelix~errors.tpl.tag.bracket.error',array('','')),
        '$aa[[234]'=>array('jelix~errors.tpl.tag.bracket.error',array('','')),
        '$aa234]'=>array('jelix~errors.tpl.tag.bracket.error',array('','')),
        'isset($t[5])'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','isset','')),
        'empty($aa)'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','empty','')),
        '$aa == 123'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','==','')),
        '$aa != 123'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','!=','')),
        '$aa >= 123'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','>=','')),
        '$aa !== 123'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','!==','')),
        '$aa <= 123'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','<=','')),
        '$aa << 123'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','<<','')),
        '$aa >> 123'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','>>','')),
        '$aa == false'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','==','')),
        '$aa == true'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','==','')),
        '$aa == null'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','==','')),
        '$aa && $bb'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','&&','')),
        '$aa || $bb'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','||','')),
        '$aa and $bb'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','and','')),
        '$aa or $bb'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','or','')),
        '$aa xor $bb'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','xor','')),
        '$aa=$bb'=>array('jelix~errors.tpl.tag.character.invalid',array('','=','')),
        '$aa+=$bb'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','+=','')),
        '$aa-=$bb'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','-=','')),
        '$aa/=$bb'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','/=','')),
        '$aa*=$bb'=>array('jelix~errors.tpl.tag.phpsyntax.invalid',array('','*=','')),
    );

    protected $badvarexprTrustedMode = array(

    );

    protected $badvarexprUnTrustedMode = array(
        '$aaa.PHP_VERSION'=>array('jelix~errors.tpl.tag.constant.notallowed',array('','PHP_VERSION','')),
    );

    function testBadVarExprTrustedMode() {
        $compil = new testJtplCompiler();
        $compil->setTrusted(true);
        foreach($this->badvarexpr as $k=>$t){
            try{
                $res = $compil->testParseVarExpr($k);
                $this->fail("No Exception for this test '$k' ");
            }catch(jException $e){
                $this->assertEqualOrDiff($t[0], $e->getLocaleKey());
                $this->assertEqual($t[1], $e->getLocaleParameters());
            }catch(Exception $e){
                $this->pass("Unknown Exception: ".$e->getMessage());
            }
        }
        foreach($this->badvarexprTrustedMode as $k=>$t){
            try{
                $res = $compil->testParseVarExpr($k);
                $this->fail("No Exception for this test '$k' ");
            }catch(jException $e){
                $this->assertEqualOrDiff($t[0], $e->getLocaleKey());
                $this->assertEqual($t[1], $e->getLocaleParameters());
            }catch(Exception $e){
                $this->pass("Unknown Exception: ".$e->getMessage());
            }
        }
    }

    function testBadVarExprUnTrustedMode() {
        $compil = new testJtplCompiler();
        $compil->setTrusted(false);
        foreach($this->badvarexpr as $k=>$t){
            try{
                $res = $compil->testParseVarExpr($k);
                $this->fail("No Exception for this test '$k' ");
            }catch(jException $e){
                $this->assertEqualOrDiff($t[0], $e->getLocaleKey());
                $this->assertEqual($t[1], $e->getLocaleParameters());
            }catch(Exception $e){
                $this->pass("Unknown Exception: ".$e->getMessage());
            }
        }
        foreach($this->badvarexprUnTrustedMode as $k=>$t){
            try{
                $res = $compil->testParseVarExpr($k);
                $this->fail("No Exception for this test '$k' ");
            }catch(jException $e){
                $this->assertEqualOrDiff($t[0], $e->getLocaleKey());
                $this->assertEqual($t[1], $e->getLocaleParameters());
            }catch(Exception $e){
                $this->pass("Unknown Exception: ".$e->getMessage());
            }
        }
    }

    protected $varTag = array(
        '$aaa|escxml' => 'htmlspecialchars($t->_vars[\'aaa\'])',
        '$aaa|jdatetime' => 'jtpl_modifier_common_jdatetime($t->_vars[\'aaa\'])',
        '$aaa|jdatetime:\'db_date\'' => 'jtpl_modifier_common_jdatetime($t->_vars[\'aaa\'],\'db_date\')',
        '$aaa|jdatetime:\'db_date\':\'lang_date\'' => 'jtpl_modifier_common_jdatetime($t->_vars[\'aaa\'],\'db_date\',\'lang_date\')',
        '$aaa|jdatetime:\'db_date\',\'lang_date\'' => 'jtpl_modifier_common_jdatetime($t->_vars[\'aaa\'],\'db_date\',\'lang_date\')',
    );


    function testVarTag() {
        $compil = new testJtplCompiler();
        $compil->setTrusted(true);
        foreach($this->varTag as $k=>$t){
            try{
                $res = $compil->testParseVariable($k);
                $this->assertEqualOrDiff($t, $res);
            }catch(jException $e){
                $this->fail("Test '$k', Unknown Jelix Exception: ".$e->getMessage().' ('.$e->getLocaleKey().')');
            }catch(Exception $e){
                $this->fail("Test '$k', Unknown Exception: ".$e->getMessage());
            }
        }
    }

}

?>
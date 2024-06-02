<?php

require 'php_deps/vendor/autoload.php';

//use PhpParser\Error;
//use PhpParser\NodeTraverser;
//use PhpParser\NodeVisitor\NameResolver;
//use PhpParser\ParserFactory;
//use PhpParser\Node;
//use PhpParser\NodeVisitorAbstract;
//use PhpParser\PrettyPrinter;

//const DEFINE_STMTS = 'defines.txt';

//class FileVisitor extends NodeVisitorAbstract
//{
//    public $requiredFiles = [];
//
//    public function enterNode(Node $node)
//    {
//        if ($node instanceof Node\Expr\Include_) {
//            if ($node->expr instanceof Node\Scalar\String_) {
//                $this->requiredFiles[] = $node->expr->value;
//            } else {
//                $this->requiredFiles[] = evalExpr($node);
//            }
//        } elseif ($node instanceof Node\Stmt\Use_) {
//            foreach ($node->uses as $use) {
//                $this->requiredFiles[] = $use->name->toString();
//            }
//        } elseif ($node instanceof Node\Stmt\Expression && $node->expr instanceof Node\Expr\FuncCall && $node->expr->name->toString() == 'define') {
//            $prettyPrinter = new PrettyPrinter\Standard;
//            $statementCode = $prettyPrinter->prettyPrintExpr($node->expr) . ';';
//            $existingStatements = file_get_contents(DEFINE_STMTS);
//            if (strpos($existingStatements, $statementCode) === false) {
//                echo "Found constant definition: $statementCode\n";
//                eval($statementCode);
//                file_put_contents(DEFINE_STMTS, $statementCode . PHP_EOL, FILE_APPEND | LOCK_EX);
//            }
//        }
//    }
//}
//
//class EvaluatedAssignmentVisitor extends NodeVisitorAbstract
//{
//    private $evaluatedValues = [];
//    private $varName;
//
//    public function __construct(string $varName)
//    {
//        $this->varName = $varName;
//    }
//
//    public function enterNode(Node $node)
//    {
//        if ($node instanceof Node\Expr\Assign && $node->var->name === $this->varName) {
//            $prettyPrinter = new PrettyPrinter\Standard;
//            $assignmentCode = $prettyPrinter->prettyPrintExpr($node) . '; return $' . $node->var->name . ';';
//            $this->evaluatedValues[] = eval($assignmentCode);
//        }
//    }
//
//    public function getEvaluatedValues(): array
//    {
//        return $this->evaluatedValues;
//    }
//
//}
//
//function evalExpr($node)
//{
//    if ($node->expr instanceof Node\Expr\BinaryOp\Concat) {
//        return evalExpr($node->expr->left) . evalExpr($node->expr->right);
//    } elseif ($node instanceof Node\Expr\Variable) {
//        $traverser = new NodeTraverser();
//        $evaluatedAssignmentVisitor = new EvaluatedAssignmentVisitor($node);
//        $traverser->addVisitor($evaluatedAssignmentVisitor);
//
//        $traverser->traverse($GLOBALS['ast']);
//        $evaluatedValues = $evaluatedAssignmentVisitor->getEvaluatedValues();
//        return $evaluatedValues;
//    } elseif ($node instanceof Node\Scalar\String_) {
//        return $node->value;
//    } else {
//        echo "Unknown node type: " . get_class($node) . "\n";
//        return '';
//    }
//}
//
//function extractRequiredFiles()
//{
//    // Resolve names
//    $traverser = new NodeTraverser;
//    $traverser->addVisitor(new NameResolver);
//
//    // Traverse AST
//    $fileVisitor = new FileVisitor();
//    $traverser->addVisitor($fileVisitor);
//
//    $traverser->traverse($GLOBALS['ast']);
//    return $fileVisitor->requiredFiles;
//}

chdir('dump');
$requiredFile = '';
$filePath = 'proc\self\cwd\index.php';
$code = file_get_contents($filePath);
$code = str_replace('__FILE__', "'$GLOBALS[filePath]'", $code);
file_put_contents($filePath, $code);
try {
    @require getcwd(). DIRECTORY_SEPARATOR . $filePath;
} catch (Throwable $e) {
    preg_match("/'(.*?)'/", $e->getMessage(), $matches);
    $missingFile = $matches[1];
    $dir = dirname($GLOBALS['filePath']);
    $missingFile = str_replace(getcwd(), $dir, $missingFile);
    $GLOBALS['requiredFile'] = $missingFile;
} finally {
    echo getcwd() . $requiredFile;
}

//$code = file_get_contents($filePath);
//$code = str_replace('__FILE__', "'$GLOBALS[filePath]'", $code);
//$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
//try {
//    $ast = $parser->parse($code);
//} catch (Error $error) {
//    echo "Parse Error: {$error->getMessage()}\n";
//    exit(1);
//}
//
//if (!file_exists(DEFINE_STMTS)) {
//    file_put_contents(DEFINE_STMTS, '');
//}
//
//$requiredFiles = extractRequiredFiles();
//
//if (empty($requiredFiles)) {
//    echo "No required files found in $filePath\n";
//} else {
//    print_r($requiredFiles);
//}

?>

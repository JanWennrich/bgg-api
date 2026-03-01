<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test\PhpStan\Rules;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use Psr\Log\LoggerInterface;

/**
 * Enforce that an "ignore all Infection errors" comment is present on all calls to a logger interface.
 * The comment is required to stop Infection from mutating logger calls, as we do not want to test those more strictly.
 *
 * Implementation taken from https://github.com/infection/infection/issues/2958#issuecomment-3896457974.
 *
 * @implements Rule<Expression>
 */
class LoggerInfectionIgnoreRule implements Rule
{
    private const string INFECTION_IGNORE_ALL_DOC_COMMENT = '/** @infection-ignore-all */';

    public function getNodeType(): string
    {
        return Expression::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->expr instanceof MethodCall) {
            return [];
        }

        $call = $node->expr;

        if (!$call->name instanceof Identifier) {
            return [];
        }

        $callerType = $scope->getType($call->var);
        $loggerType = new ObjectType(LoggerInterface::class);

        $isLoggerInterface = $loggerType->equals($callerType);
        $isLoggerImplementation = $loggerType->isSuperTypeOf($callerType)->yes();

        if (!$isLoggerInterface && !$isLoggerImplementation) {
            return [];
        }

        if (array_any(
            $node->getComments(),
            fn($comment) => $comment instanceof Comment\Doc && trim(
                $comment->getText()
            ) === self::INFECTION_IGNORE_ALL_DOC_COMMENT
        )
        ) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf('Call to LoggerInterface::%s() must be ignored by infection.', $call->name->toString()),
            )->identifier('infection.logger.ignore')
                ->tip(sprintf('Prefix call by %s', self::INFECTION_IGNORE_ALL_DOC_COMMENT))
                ->line($node->getStartLine())
                ->build(),
        ];
    }
}

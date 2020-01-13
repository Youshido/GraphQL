<?php
/**
 * Date: 10/24/16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\RequestValidator;


use Youshido\GraphQL\Exception\Parser\InvalidRequestException;
use Youshido\GraphQL\Execution\Request;

class RequestValidator implements RequestValidatorInterface
{

    /**
     * @param Request $request
     *
     * @throws InvalidRequestException
     */
    public function validate(Request $request)
    {
        $this->assertFragmentReferencesValid($request);
        $this->assertFragmentsUsed($request);
        $this->assertAllVariablesExists($request);
        $this->assertAllVariablesUsed($request);
    }

    /**
     * @param Request $request
     *
     * @throws InvalidRequestException
     */
    private function assertFragmentsUsed(Request $request)
    {
        foreach ($request->getFragmentReferences() as $fragmentReference) {
            $request->getFragment($fragmentReference->getName())->setUsed(true);
        }

        foreach ($request->getFragments() as $fragment) {
            if (!$fragment->isUsed()) {
                throw new InvalidRequestException(sprintf('Fragment "%s" is not used', $fragment->getName()), $fragment->getLocation());
            }
        }
    }

    /**
     * @param Request $request
     *
     * @throws InvalidRequestException
     */
    private function assertFragmentReferencesValid(Request $request)
    {
        foreach ($request->getFragmentReferences() as $fragmentReference) {
            if (!$request->getFragment($fragmentReference->getName())) {
                throw new InvalidRequestException(sprintf('Fragment "%s" is not defined in query', $fragmentReference->getName()), $fragmentReference->getLocation());
            }
        }
    }

    /**
     * @param Request $request
     *
     * @throws InvalidRequestException
     */
    private function assertAllVariablesExists(Request $request)
    {
        foreach ($request->getVariableReferences() as $variableReference) {
            if (!$variableReference->getVariable()) {
                throw new InvalidRequestException(sprintf('Variable "%s" does not exists', $variableReference->getName()), $variableReference->getLocation());
            }
        }
    }

    /**
     * @param Request $request
     *
     * @throws InvalidRequestException
     */
    private function assertAllVariablesUsed(Request $request)
    {
        foreach ($request->getQueryVariables() as $queryVariable) {
            if (!$queryVariable->isUsed()) {
                throw new InvalidRequestException(sprintf('Variable "%s" is not used', $queryVariable->getName()), $queryVariable->getLocation());
            }
        }
    }
}

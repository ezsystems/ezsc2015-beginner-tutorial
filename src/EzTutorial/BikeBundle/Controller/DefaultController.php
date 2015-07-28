<?php

namespace EzTutorial\BikeBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\ValueObject;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $rideList = $this->findRidesAction(65);
        return $this->render('EzTutorialBikeBundle:Default:index.html.twig', array('name' => $name, 'rideList' => $rideList));
    }

    /**
     * @return Response
     */
    public function listRidesAction()
    {
        $response = new Response();
        $rides = null;


        // Using the criteria helper (a demobundle custom service) to generate our query's criteria.
        // This is a good practice in order to have less code in your controller.
        $criteria = new Criteria();

        // Generating query
        $query = new Query();
        $query->criterion = $criteria;
        $query->sortClauses = array(
            new SortClause\Field('ride', 'creation_date', Query::SORT_DESC)
        );

        $rides = $this->searchService->findContent($query);

        return $this->render(
            'EzTutorialBikeBundle:frontpage.html.twig',
            array(
                'content' => $rides,
            ),
            $response
        );

    }

    /**
     * @param int $folderId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws NotFoundHttpException
     */
    public function findRidesAction($folderId)
    {

        // Getting location and content from ezpublish dedicated services
        $repository = $this->getRepository();
        $location = $repository->getLocationService()->loadLocation($folderId);

        // Invisibility check (best practice)
        if ($location->invisible)
        {
            throw new NotFoundHttpException("Location #$folderId cannot be displayed as it is flagged as invisible.");
        }

        // Getting language for the current siteaccess
        $languages = $this->getConfigResolver()->getParameter('languages');

        //generating Criteria
        $criteria = array();
        $criteria[] = new Criterion\Visibility(Criterion\Visibility::VISIBLE);
        $criteria[] = new Criterion\Subtree($location->pathString);
        $criteria[] = new Criterion\ContentTypeIdentifier(array('ride'));
        $criteria[] = new Criterion\LanguageCode($languages);

        // Generating query to retrieve only Rides
        $query = new LocationQuery();
        $query->criterion = new Criterion\LogicalAnd(
            array(
                new Criterion\ParentLocationId($folderId),
                new Criterion\ContentTypeIdentifier(array('ride'))
            )
        );
        $searchService = $repository->getSearchService();
        $content = $searchService->findContent($query);

        // Building my Ride list
        $rideList = array();
        foreach ($content->searchHits as $result)
        {
            if ($result->valueObject instanceof ValueObject)
            {
                $rideList[] = array(
                    'id' => $result->valueObject->contentInfo->id,
                    'title' => $result->valueObject->contentInfo->name,
                    'starting_point' => $result->valueObject->getFieldValue('starting_point'),
                    'difficulty' => $result->valueObject->getFieldValue('difficulty'),
                    'length' => $result->valueObject->getFieldValue('length'),
                );

            }
        }
        return $rideList;
    }

}

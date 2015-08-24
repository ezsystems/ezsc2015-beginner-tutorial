<?php

namespace EzSummer\BikeBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

class RideController extends Controller
{
    public function listAction(Location $location, $viewType, $layout = false, $params = [])
    {
        $searchService = $this->getRepository()->getSearchService();
        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            new Criterion\ContentTypeIdentifier('ride'),
            new Criterion\ParentLocationId($location->id),
        ]);
        $query->sortClauses = [new SortClause\DatePublished()];
        $result = $searchService->findLocations($query);

        return $this->get('ez_content')->viewLocation(
            $location->id,
            $viewType,
            $layout,
            $params + [
                'rides' => $result->searchHits
            ]
        );
    }
}

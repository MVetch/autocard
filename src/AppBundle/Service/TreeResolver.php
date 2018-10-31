<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 04.06.2018
 * Time: 12:18
 */

namespace AppBundle\Service;


class TreeResolver
{

    protected $map = [];

    public function __construct($allIds)
    {
        $this->setMap($allIds);
    }

    public function getMap()
    {
        return $this->map;
    }

    public function setMap($allIds)
    {
        $map = [];

        foreach ($allIds as & $user) {
            $user['parent_id'] = (int) $user['parent_id'];
            $user['id'] = (int) $user['id'];
        }

        foreach ($allIds as $user) {
            if ($user['parent_id'] == null) {
                $map[$user['id']] = [];
            }
        }

        foreach ($map as $mappedId => $mappedChildren) {
            foreach ($allIds as $user) {
                if ($mappedId == $user['parent_id']) {
                    $map[$mappedId][$user['id']] = [];
                }
            }
        }

        //dump($map);die();

        foreach ($map as $mappedId => $mappedChildren) {
            foreach ($mappedChildren as $mappedIdLevel1 => $mappedChildrenLevel1) {
                foreach ($allIds as $user) {
                    if ($mappedIdLevel1 == $user['parent_id']) {
                        $map[$mappedId][$mappedIdLevel1][$user['id']] = [];
                    }
                }
            }
        }

        //dump($map);die();
//
        foreach ($map as $mappedId => $mappedChildren) {
            foreach ($mappedChildren as $mappedIdLevel1 => $mappedChildrenLevel1) {
                foreach ($mappedChildrenLevel1 as $mappedIdLevel2 => $mappedChildrenLevel2) {
                    foreach ($allIds as $user) {
                        if ($mappedIdLevel2 == $user['parent_id']) {
                            $map[$mappedId][$mappedIdLevel1][$mappedIdLevel2][$user['id']] = [];
                        }
                    }
                }
            }
        }

        foreach ($map as $mappedId => $mappedChildren) {
            foreach ($mappedChildren as $mappedIdLevel1 => $mappedChildrenLevel1) {
                foreach ($mappedChildrenLevel1 as $mappedIdLevel2 => $mappedChildrenLevel2) {
                    foreach ($mappedChildrenLevel2 as $mappedIdLevel3 => $mappedChildrenLevel3) {
                        foreach ($allIds as $user) {
                            if ($mappedIdLevel3 == $user['parent_id']) {
                                $map[$mappedId][$mappedIdLevel1][$mappedIdLevel2][$mappedIdLevel3][$user['id']] = [];
                            }
                        }
                    }
                }
            }
        }

        foreach ($map as $mappedId => $mappedChildren) {
            foreach ($mappedChildren as $mappedIdLevel1 => $mappedChildrenLevel1) {
                foreach ($mappedChildrenLevel1 as $mappedIdLevel2 => $mappedChildrenLevel2) {
                    foreach ($mappedChildrenLevel2 as $mappedIdLevel3 => $mappedChildrenLevel3) {
                        foreach ($mappedChildrenLevel3 as $mappedIdLevel4 => $mappedChildrenLevel4) {
                            foreach ($allIds as $user) {
                                if ($mappedIdLevel4 == $user['parent_id']) {
                                    $map[$mappedId][$mappedIdLevel1][$mappedIdLevel2][$mappedIdLevel3][$mappedIdLevel4][$user['id']] = [];
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($map as $mappedId => $mappedChildren) {
            foreach ($mappedChildren as $mappedIdLevel1 => $mappedChildrenLevel1) {
                foreach ($mappedChildrenLevel1 as $mappedIdLevel2 => $mappedChildrenLevel2) {
                    foreach ($mappedChildrenLevel2 as $mappedIdLevel3 => $mappedChildrenLevel3) {
                        foreach ($mappedChildrenLevel3 as $mappedIdLevel4 => $mappedChildrenLevel4) {
                            foreach ($mappedChildrenLevel4 as $mappedIdLevel5 => $mappedChildrenLevel5) {
                                foreach ($allIds as $user) {
                                    if ($mappedIdLevel5 == $user['parent_id']) {
                                        $map[$mappedId][$mappedIdLevel1][$mappedIdLevel2][$mappedIdLevel3][$mappedIdLevel4][$mappedIdLevel5][$user['id']] = [];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($map as $mappedId => $mappedChildren) {
            foreach ($mappedChildren as $mappedIdLevel1 => $mappedChildrenLevel1) {
                foreach ($mappedChildrenLevel1 as $mappedIdLevel2 => $mappedChildrenLevel2) {
                    foreach ($mappedChildrenLevel2 as $mappedIdLevel3 => $mappedChildrenLevel3) {
                        foreach ($mappedChildrenLevel3 as $mappedIdLevel4 => $mappedChildrenLevel4) {
                            foreach ($mappedChildrenLevel4 as $mappedIdLevel5 => $mappedChildrenLevel5) {
                                foreach ($mappedChildrenLevel5 as $mappedIdLevel6 => $mappedChildrenLevel6) {
                                    foreach ($allIds as $user) {
                                        if ($mappedIdLevel6 == $user['parent_id']) {
                                            $map[$mappedId][$mappedIdLevel1][$mappedIdLevel2][$mappedIdLevel3][$mappedIdLevel4][$mappedIdLevel5][$mappedIdLevel6][$user['id']] = [];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($map as $mappedId => $mappedChildren) {
            foreach ($mappedChildren as $mappedIdLevel1 => $mappedChildrenLevel1) {
                foreach ($mappedChildrenLevel1 as $mappedIdLevel2 => $mappedChildrenLevel2) {
                    foreach ($mappedChildrenLevel2 as $mappedIdLevel3 => $mappedChildrenLevel3) {
                        foreach ($mappedChildrenLevel3 as $mappedIdLevel4 => $mappedChildrenLevel4) {
                            foreach ($mappedChildrenLevel4 as $mappedIdLevel5 => $mappedChildrenLevel5) {
                                foreach ($mappedChildrenLevel5 as $mappedIdLevel6 => $mappedChildrenLevel6) {
                                    foreach ($mappedChildrenLevel6 as $mappedIdLevel7 => $mappedChildrenLevel7) {
                                        foreach ($allIds as $user) {
                                            if ($mappedIdLevel6 == $user['parent_id']) {
                                                $map[$mappedId][$mappedIdLevel1][$mappedIdLevel2][$mappedIdLevel3][$mappedIdLevel4][$mappedIdLevel5][$mappedIdLevel6][$mappedIdLevel7][$user['id']] = [];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->map = $map;
    }

    public function getNearestParentIds($nodeId)
    {
        $chain = $this->findNode($nodeId);
    }

    public function containsNode($chain, $node)
    {
        return $this->findNode($node, $chain) !== false;
    }

    public function findNode($nodeId, $nodes = null, $level = 0)
    {
        if ($level == 0 && $nodes === null) {
            $nodes = $this->map;
        }

        foreach ($nodes as $nextNodeId => $nextNodeValues) {
            if ($nodeId == $nextNodeId) {
                return $nextNodeValues;
            }
        }

        foreach ($nodes as $nextNodeId => $nextNodeValues) {
            $node = $this->findNode($nodeId, $nextNodeValues, $level + 1);

            if (!is_null($node) && is_array($node)) {
                return $node;
            }
        }

        return false;
    }

}
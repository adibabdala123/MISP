<?php

class CyclicGraphException extends Exception {}

class GraphProperties
{
    public function __construct($graphData)
    {
        $this->numberNodes = count($graphData);
        $this->graph = $this->_buildEdgeList($graphData);
        $this->properties = [];
    }

    private function _buildEdgeList($graphData): array
    {
        $list = [];
        foreach ($graphData as $node) {
            $list[(int)$node['id']] = [];
            foreach (($node['outputs'] ?? []) as $output_id => $outputs) {
                foreach ($outputs as $connections) {
                    foreach ($connections as $connection) {
                        $list[$node['id']][] = (int)$connection['node'];
                    }
                }
            }
        }
        return $list;
    }

    private function _DFSUtil($node_id, &$color, $depth=0): bool
    {
        $color[$node_id] = 'GRAY';
        foreach ($this->graph[$node_id] as $i) {
            if ($color[$i] == 'GRAY') {
                $this->properties[] = [$node_id, $i, __('Cycle')];
                return true;
            }
            if ($color[$i] == 'WHITE' && $this->_DFSUtil($i, $color, $depth+1)) {
                if ($depth > 0) {
                    $this->properties[] = [$node_id, $i, __('Cycle')];
                }
                return true;
            }
        }
        $color[$node_id] = 'BLACK';
        return false;
    }

    /**
     * isCyclic Return is the graph is cyclic, so if it contains a cycle.
     * 
     * A directed graph G is acyclic if and only if a depth-first search of G yields no back edges.
     * Introduction to Algorithms, third edition By Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest, Clifford Stein
     *
     * @return array
     */
    public function isCyclic(): array
    {
        $this->properties = [];
        $color = [];
        foreach (array_keys($this->graph) as $node_id) {
            $color[$node_id] = 'WHITE';
        }
        foreach (array_keys($this->graph) as $node_id) {
            if ($color[$node_id] == 'WHITE') {
                if ($this->_DFSUtil($node_id, $color)) {
                    return [true, $this->properties];
                }
            }
        }
        return [false, []];
    }
}

class WorkflowGraphTool
{

    const GRAPH_BLOCKING_CONNECTION_NAME = 'output_1';
    const GRAPH_NON_BLOCKING_CONNECTION_NAME = 'output_2';

    /**
     * extractTriggersFromWorkflow Return the list of trigger names (or full node) that are specified in the workflow
     *
     * @param  array $workflow
     * @param  bool $fullNode
     * @return array
     */
    public static function extractTriggersFromWorkflow(array $graphData, bool $fullNode = false): array
    {
        $triggers = [];
        foreach ($graphData['data'] as $node) {
            if ($node['data']['module_type'] == 'trigger') {
                if (!empty($fullNode)) {
                    $triggers[] = $node;
                } else {
                    $triggers[] = $node['data']['id'];
                }
            }
        }
        return $triggers;
    }

    /**
     * triggerHasBlockingPath Return if the provided trigger has an edge leading to a blocking path
     * 
     * @param array $node
     * @returns bool
     */
    public static function triggerHasBlockingPath(array $node): bool
    {
        return !empty($node['outputs'][WorkflowGraphTool::GRAPH_BLOCKING_CONNECTION_NAME]['connections']);
    }

    /**
     * triggerHasBlockingPath Return if the provided trigger has an edge leading to a non-blocking path
     * 
     * @param array $node
     * @returns bool
     */
    public static function triggerHasNonBlockingPath(array $node): bool
    {
        return !empty($node['outputs'][WorkflowGraphTool::GRAPH_NON_BLOCKING_CONNECTION_NAME]['connections']);
    }

    /**
     * hisCyclic Return if the graph contains a cycle
     *
     * @param array $graphData
     * @param array $cycles Get a list of cycle
     * @return boolean
     */
    public static function isAcyclic(array $graphData, array &$cycles=[]): bool
    {
        $graphProperties = new GraphProperties($graphData);
        $result = $graphProperties->isCyclic();
        $isCyclic = $result[0];
        $cycles = $result[1];
        return !$isCyclic;
        // try {
        //     WorkflowGraphTool::buildExecutionPath($graphData);
        // } catch (CyclicGraphException $e) {
        //     return false;
        // }
        // return true;
    }

    // /**
    //  * buildExecutionPath Return the execution path for the provided workflow graph
    //  * 
    //  * @param array $graphData
    //  * @returns array
    //  */
    // public static function buildExecutionPath(array $graphData): array
    // {
    //     $starting_nodes = [];
    //     $nodesById = [];
    //     foreach ($graphData as $node) { // Re-index data by node ID
    //         $nodesById[$node['id']] = $node;
    //         if (empty($node['inputs']) && !empty($node['outputs'])) { // collect nodes acting as starting point (no input and at least one output)
    //             $starting_nodes[] = $node;
    //         }
    //     }

    //     // construct execution flow following outputs/inputs of each  blocks
    //     $processedNodeIDs = [];
    //     foreach (array_keys($starting_nodes) as $i) {
    //         WorkflowGraphTool::buildExecutionPathViaConnections($starting_nodes[$i], $nodesById, $processedNodeIDs);
    //     }
    //     $execution_path = [];
    //     foreach ($starting_nodes as $node) {
    //         $execution_path[] = WorkflowGraphTool::cleanNode($node);
    //     }
    //     return $execution_path;
    // }

    // public static function buildExecutionPathViaConnections(&$node, $nodesById, &$processedNodeIDs)
    // {
    //     if (!empty($processedNodeIDs[$node['id']])) { // Prevent infinite loop
    //         throw new CyclicGraphException(__('The graph contains a cycle leading to node %s (%s)', $node['name'], $node['id']));
    //     }
    //     $processedNodeIDs[$node['id']] = true;
    //     if (!empty($node['outputs'])) {
    //                     // $ode['next'][] = $this->cleanNode($nextNode);
    //                     $node['next'][$output_id] = WorkflowGraphTool::cleanNode($nextNode);
    //                 }
    //             }
    //         }
    //     }
    //     return $node;
    // }

    // public static function cleanNode($node): array
    // {
    //     return [
    //         'id' => $node['id'],
    //         // 'data' => $node['data'],
    //         'data' => [
    //             'name' => $node['data']['name'],
    //             'inputs' => $node['data']['inputs'] ?? [],
    //             'outputs' => $node['data']['outputs'] ?? [],
    //         ],
    //         // 'module_data' => $this->moduleByID[$node['data']['id']],
    //         'next' => $node['next'] ?? [],
    //     ];
    // }
}

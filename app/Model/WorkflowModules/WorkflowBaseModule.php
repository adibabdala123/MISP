<?php
class WorkflowBaseModule
{
    public $id = 'to-override';
    public $name = 'to-override';
    public $description = 'to-override';
    public $icon = '';
    public $icon_class = '';
    public $inputs = 0;
    public $outputs = 0;
    public $params = [];

    /** @var PubSubTool */
    private static $loadedPubSubTool;

    public function __construct()
    {
    }

    protected function getParams($node): array
    {
        $indexedParam = [];
        foreach ($node['data']['params'] as $param) {
            $indexedParam[$param['label']] = $param;
        }
        return $indexedParam;
    }

    public function getConfig(): array
    {
        $reflection = new ReflectionObject($this);
        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            if ($property->isPublic()) {
                $properties[$property->getName()] = $property->getValue($this);
            }
        }
        return $properties;
    }

    public function exec(array $node): bool
    {
        $this->push_zmq([
            'Executing module' => $this->name,
            'timestamp' => time(),
        ]);
        return true;
    }

    public function push_zmq($message)
    {
        if (!self::$loadedPubSubTool) {
            App::uses('PubSubTool', 'Tools');
            $pubSubTool = new PubSubTool();
            $pubSubTool->initTool();
            self::$loadedPubSubTool = $pubSubTool;
        }
        $pubSubTool = self::$loadedPubSubTool;
        $pubSubTool->workflow_push($message);
    }

    public function checkLoading()
    {
        return 'The Factory Must Grow';
    }
}
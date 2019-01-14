<?php

interface Visit {
	public function go($location);
}

class Travller
{
	protected $trafficTool;

	public function __construct(Visit $trafficTool)
	{
		$this->trafficTool = $trafficTool;
	}

	public function visitTibet()
	{
		$this->trafficTool->go();
	}
}

class Train implements Visit
{
	public function go($location)
	{
		echo "go to $location by train";
	}
}

class Car implements Visit
{
	public function go($location)
	{
		echo "drive Car to $location";
	}
}
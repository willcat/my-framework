<?php 
class Container
{
    //用于装载回调函数，实际容器还会装载实例等其他内容，从而实现单例等高级功能
    protected $bindings = [];

    /**
     * 绑定接口和生成相应实例的回调函数
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bing($abstract, $concrete=null, $shared=false)
    {
        if (! $concrete instanceof Closure) {
            //如果提供的参数不是回调函数，则产生默认的回调函数
            $concrete = $this->getClosure($abstract, $concrete);
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * 生成实例的回调函数，
     * 
     */
    protected function getClosure($abstract, $concrete)
    {
        return function($c) use ($abstract, $concrete)
        {
            $method = ($abstract == $concrete) ? 'build' : 'make';
            return $c->$method($concrete);
        };
    }

    //生成实例对象，首先解决接口和要实例化类之间的依赖关系
    public function make($abstract)
    {
        $concrete = $this->getConceret($abstract);

        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete);
        } else {
            $object = $this->make($concrete);
        }

        return $object;
    }
}
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
    public function bind($abstract, $concrete=null, $shared=false)
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

    /**
     * 一个concrete和abstract完全相等或者是一个闭包的时候就是可以build的
     */
    protected function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    //获取绑定的回调函数
    protected function getConceret($abstract)
    {
        if (! isset($this->bindings[$abstract]))
        {
            return $abstract;
        }

        return $this->bindings[$abstract]['concrete'];
    }



    protected function build($concrete)
    {
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        $reflector = new ReflectionClass($concrete);
        if (! $reflector->isInstantiable() ) {
            echo $message =  "Target [$concrete] is not instantiable.";
        }

        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            //无构造函数，直接new
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();
        $instances = $this->getDependencies($dependencies);
        return $reflector->newInstanceArgs($instances);
    }

    //通过反射机制获取实例化对象所需的依赖
    protected function getDependencies($parameters)
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();

            if (is_null($dependency)) {
                //不是类
                $dependencies[] = NULL;
            } else {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }

        return (array) $dependencies;
    }

    protected function resolveClass(ReflectionParameter $parameter)
    {
        return $this->make($parameter->getClass()->name);
    }

}
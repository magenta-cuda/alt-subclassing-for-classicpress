# alt-subclassing-for-classicpress

**Although this was originally [intended for ClassicPress](https://github.com/ClassicPress/ClassicPress/issues/749) this idea is equally applicable to WordPress.**

I find the middleware of Redux extremely useful. The essence of Redux middleware is function wrapping. I think function wrapping can be also be easily done in WordPress as a replacement in WordPress for pluggable functions and subclassed methods of PHP classes.

## Pluggable Functions

A wrapper of a function can pre/post process the call to the function or replace the call to the function. This can be easily be done in WordPress:

#### Transform:

```
if ( ! function_exists( 'omega' ) ) :
    function omega( $gamma ) {
        $result = $gamma + 1;
        return $result;
    }
endif;

```

#### to:


```
if ( ! function_exists( 'omega' ) ) :
    function omega( $gamma ) {
        $delta = apply_filters( 'omega', 'omega0' );
        return call_user_func( $delta, $gamma );
    }

    function omega0( $gamma ) {
        $result = $gamma + 1;
        return $result;
    }
endif;

```

#### Install wrappers for omega() or replace omega().

```
if ( ! isset( $_GET[ 'mc_replace' ] ) ) {
    # add some wrappers of omega()

    add_filter( 'omega', function( $beta ) {
        $inner_beta1 = $beta;
        return function( $gamma ) use ( $inner_beta1 ) {
            $gamma1 = $gamma + 1;
            $result = call_user_func( $inner_beta1, $gamma1 );
            $result1 = $result + 10;
            return $result1;
        };
    }, 100 );

    add_filter( 'omega', function( $beta ) {
        $inner_beta2 = $beta;
        return function( $gamma ) use ( $inner_beta2 ) {
            $gamma1 = $gamma + 1;
            $result = call_user_func( $inner_beta2, $gamma1 );
            $result1 = $result + 100;
            return $result1;
        };
    }, 200 );

} else {
    # or just replace omega()

    add_filter( 'omega', function( $beta ) {
        return function( $gamma ) {
            $result = $gamma + 0.1;
            return $result;
        };
    } );
}
```

Although, the technique can be applied to any global function, it is very easy to mechanically transform all pluggable functions with a small script. In my opinion it is better than pluggable functions since it allows for modification of the function instead of just replacement.

# Subclassing

I am often frustrated using action/filter hooks - no appropriate hook exists or even if a hook exists it doesn't provide sufficient context in the arguments. For me, subclassing is a very useful alternative to action/filter hooks. It allows us to install wrappers on the methods of the class that can pre/post process the call to the method or replace the call to the method:

```
class Alpha {
    public $epsilon = 0;

    public function beta( $gamma ) {
        ++$this->epsilon;
        return $this->epsilon + $gamma;
    }
}

class Alpha1 extends Alpha {
    public function beta( $gamma ) {
        $this->epsilon += 10;
        $gamma += 100;
        return Alpha::beta( $gamma ) + 1000;
    }
}

```
The problem with subclassing is the difficulty of installing multiple subclasses of a class. Since the links in the chain of inheritance is specified by the extends clause in the class declaration, then a subclass needs to explicitly specify the next link. Contrast this with action/filter hooks which are installed by priority. With respect to installation action/filter hooks are independent of each other. My proposed solution is way to subclass the methods of a class but with the same ease of installation as action/filter hooks.

#### Transform:
```
class Alpha {
    public $epsilon = 0;

    public function beta( $gamma ) {
        ++$this->epsilon;
        return $this->epsilon + $gamma;
    }
}

```
#### to:

```
class Alpha {

    public $epsilon = 0;

    # beta() now executes an inheritance chain for beta()
    public function beta( $gamma ) {
        # dynamically create a psuedo inheritance chain for beta()
        $delta = apply_filters( 'alpha_beta', [ $this, 'beta0' ] );
        # call the head of the inheritance chain
        if ( is_array( $delta ) ) {
            return call_user_func( $delta,        $gamma );
        } else {
            return call_user_func( $delta, $this, $gamma );
        }
    }

    # beta0() is the original beta()
    public function beta0( $gamma ) {
        ++$this->epsilon;
        return $this->epsilon + $gamma;
    }
}

```
Then multiple independent with respect to installation wrappers of beta() can be installed:

```
add_filter( 'alpha_beta', function( $beta ) {
    $inner_beta = $beta;
    # return a wrapper of the function $inner_beta()
    return function( $_this, $gamma )  use ( $inner_beta ) {
        ++$_this->epsilon;
        $gamma += 100;
        # call the next link in the inheritance chain
        if ( is_array( $inner_beta ) ) {
            $result = call_user_func( $inner_beta,         $gamma );
        } else {
            $result = call_user_func( $inner_beta, $_this, $gamma );
        }
        return result + 1000;
    };
}, 100 );

add_filter( 'alpha_beta', function( $beta ) {
    $inner_beta = $beta;
    # return a wrapper of the function $inner_beta()
    return function( $_this, $gamma ) use ( $inner_beta ) {
        $_this->epsilon += 1;
        $gamma += 10;
        # call the next link in the inheritance chain
        if ( is_array( $inner_beta ) ) {
            $result = call_user_func( $inner_beta,         $gamma );
        } else {
            $result = call_user_func( $inner_beta, $_this, $gamma );
        }
        return $result + 10000;
    };
}, 200 );
```

Here the inheritance chain is dynamically created at execution time. It is not necessary to specify the next link in the source code. This is completely backward compatible. No change in the source code is need for code that uses the class Alpha - the call to beta() is unchanged. Further, the transformation of Alpha can be done programatically (i.e., a programmer is not needed) - a small script can create the wrapper method beta() with the apply filters() call and rename the original beta() to beta0(). N.B. The properties accessed by the wrappers must be declared public, which is a negative to consider.

This example is specifically for non-static methods of classes. A small modification will make it work with static methods of classes.

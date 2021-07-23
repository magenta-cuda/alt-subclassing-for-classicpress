<?php
/*
 * Plugin Name: Alt Subclassing for ClassicPress Experiment
 * Description: Function wrapping as a complementary alternative to pluggable
 *              functions and subclassed methods of classes.
 *              Proof of concept for ClassicPress issue #749.
 * Author: Magenta Cuda
 */

# Alternative to pluggable functions
# Transform:

/*
if ( ! function_exists( 'omega' ) ) :
    function omega( $gamma ) {
        $result = $gamma + 1;
        return $result;
    }
endif;
 */

# to:

if ( ! function_exists( 'omega' ) ) :
    function omega( $gamma ) {
        $delta = apply_filters( 'omega', 'omega0' );
        return call_user_func( $delta, $gamma );
    }

    function omega0( $gamma ) {
        error_log( "omega0():BACKTRACE = \n" . str_replace( ', ', "\n", wp_debug_backtrace_summary() ) );
        error_log( 'omega0():$gamma = ' . $gamma );
        $result = $gamma + 1;
        error_log( 'omega0():$result = ' . $result );
        return $result;
    }
endif;

# Install wrappers for omega() or replace omega().

if ( ! isset( $_GET[ 'mc_replace' ] ) ) {
    # add some wrappers of omega()

    add_filter2( 'omega', 'omega-omega1', function( $beta ) {
        $inner_beta1 = $beta;
        error_log( 'installing omega1():$inner_beta1: ' . print_r( $inner_beta1, true ) );
        return function( $gamma ) use ( $inner_beta1 ) {
            $gamma1 = $gamma + 1;
            error_log( 'omega1():$gamma: ' . $gamma . ' -> ' . $gamma1 );
            $result = call_user_func( $inner_beta1, $gamma1 );
            $result1 = $result + 10;
            error_log( 'omega1():$result: ' . $result . ' -> ' . $result1 );
            return $result1;
        };
    }, 100 );

    add_filter2( 'omega', 'omega-omega2', function( $beta ) {
        $inner_beta2 = $beta;
        error_log( 'installing omega2():$inner_beta2: ' . print_r( $inner_beta2, true ) );
        return function( $gamma ) use ( $inner_beta2 ) {
            $gamma1 = $gamma + 1;
            error_log( 'omega2():$gamma: ' . $gamma . ' -> ' . $gamma1 );
            $result = call_user_func( $inner_beta2, $gamma1 );
            $result1 = $result + 100;
            error_log( 'omega2():$result: ' . $result . ' -> ' . $result1 );
            return $result1;
        };
    }, 200 );

} else {
    # just replace omega()

    add_filter2( 'omega', 'omega-omega0', function( $beta ) {
        return function( $gamma ) {
            $result = $gamma + 0.1;
            return $result;
        };
    } );
}

# Alternative to Subclassing
# Transform:

/*
class Alpha {
    public $epsilon = 0;
    public function beta( $gamma ) {
        $this->epsilon += 1;
        $result = $this->epsilon + $gamma;
        return $result;
    }
}
 */

# to:

class Alpha {
    public $epsilon = 0;

    public function beta( $gamma ) {
        error_log( 'beta():$gamma = ' . $gamma );
        $delta = apply_filters( 'alpha_beta', [ $this, 'beta0' ] );
        error_log( 'beta():$delta = ' . print_r( $delta, true ) );
        if ( is_array( $delta ) ) {
            return call_user_func( $delta, $gamma );
        } else {
            return call_user_func( $delta, $this, $gamma );
        }
    }

    public function beta0( $gamma ) {
        error_log( "beta0():BACKTRACE = \n" . str_replace( ', ', "\n", wp_debug_backtrace_summary() ) );
        error_log( 'beta0():$gamma = ' . $gamma );
        $epsilon = $this->epsilon;
        $this->epsilon += 1;
        error_log( 'beta0():$this->epsilon: ' . $epsilon . ' -> ' . $this->epsilon );
        $result = $this->epsilon + $gamma;
        error_log( 'beta0():$result = ' . $result );
        return $result;
    }
}

# add some wrappers of Alpha::beta()

add_filter2( 'alpha_beta', 'alpha::beta-beta1', function( $beta ) {
    error_log( 'installing beta1():$beta = ' . print_r( $beta, true ) );
    $inner_beta1 = $beta;
    return function( $_this, $gamma )  use ( $inner_beta1 ) {
        $gamma1 = $gamma + 100;
        error_log( 'beta1():$gamma: ' . $gamma . ' -> ' . $gamma1 );
        $epsilon = $_this->epsilon;
        $_this->epsilon += 1;
        error_log( 'beta1():$_this->epsilon: ' . $epsilon . ' -> ' . $_this->epsilon );
        # error_log( 'beta1():$inner_beta1 = ' . print_r( $inner_beta1, true ) );
        if ( is_array( $inner_beta1 ) ) {
            $result = call_user_func( $inner_beta1,         $gamma1 );
        } else {
            $result = call_user_func( $inner_beta1, $_this, $gamma1 );
        }
        $result1 = $result + 1000;
        error_log( 'beta1():$result: ' . $result . ' -> ' . $result1 );
        return $result1;
    };
}, 100 );

add_filter2( 'alpha_beta', 'alpha::beta-beta2', function( $beta ) {
    error_log( 'installing beta2():$beta = ' . print_r( $beta, true ) );
    $inner_beta2 = $beta;
    return function( $_this, $gamma ) use ( $inner_beta2 ) {
        $gamma2 = $gamma + 10;
        error_log( 'beta2():$gamma: ' . $gamma . ' -> ' . $gamma2 );
        $epsilon = $_this->epsilon;
        $_this->epsilon += 1;
        error_log( 'beta2():$_this->epsilon: ' . $epsilon . ' -> ' . $_this->epsilon );
        # error_log( 'beta2():$inner_beta2 = ' . print_r( $inner_beta2, true ) );
        if ( is_array( $inner_beta2 ) ) {
            $result = call_user_func( $inner_beta2,         $gamma2 );
        } else {
            $result = call_user_func( $inner_beta2, $_this, $gamma2 );
        }
        $result2 = $result + 10000;
        error_log( 'beta2():$result: ' . $result . ' -> ' . $result2 );
        return $result2;
    };
}, 200 );

$hook_handlers = [];

function add_filter2( $tag, $handler, $function_to_add, $priority = 10, $accepted_args = 1 ) {
    global $hook_handlers;
    $hook_handlers[ $handler ] = $function_to_add;
    return add_filter( $tag, $function_to_add, $priority, $accepted_args );
}

function remove_filter2( $tag, $handler, $priority = 10 ) {
    global $hook_handlers;
    $function_to_remove = $hook_handlers[ $handler ];
    return remove_filter( $tag, $function_to_remove, $priority );
}

add_action( 'init', function() {

    error_log( '#######################################################################################' );
    error_log( 'omega( 0 ) = ' . omega( 0 ) );
    error_log( '#######################################################################################' );
    $alpha  = new Alpha();
    error_log( '$alpha->beta( 0 ) = ' . $alpha->beta( 0 ) );
    error_log( '#######################################################################################' . "\n\n\n\n\n" );
} );

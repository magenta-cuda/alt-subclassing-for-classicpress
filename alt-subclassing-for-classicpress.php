<?php
/*
 * Plugin Name: Alt Subclassing for ClassicPress Experiment
 * Description: Proof of concept for ClassicPress issue #749.
 * Author: Magenta Cuda
 */

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

add_filter( 'omega', function( $beta ) {
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

add_filter( 'omega', function( $beta ) {
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
        ++$this->epsilon;
        error_log( 'beta0():$this = ' . print_r( $this, true ) );
        $result = $this->epsilon + $gamma;
        error_log( 'beta0():$result = ' . $result );
        return $result;
    }
}

add_filter( 'alpha_beta', function( $beta ) {
    error_log( 'installing beta1():$beta = ' . print_r( $beta, true ) );
    $inner_beta1 = $beta;
    return function( $_this1, $gamma )  use ( $inner_beta1 ) {
        $gamma1 = $gamma + 100;
        error_log( 'beta1():$gamma: ' . $gamma . ' -> ' . $gamma1 );
        ++$_this1->epsilon;
        error_log( 'beta1():$_this1 = ' . print_r( $_this1, true ) );
        error_log( 'beta1():$inner_beta1 = ' . print_r( $inner_beta1, true ) );
        if ( is_array( $inner_beta1 ) ) {
            $result = call_user_func( $inner_beta1,         $gamma1 );
        } else {
            $result = call_user_func( $inner_beta1, $_this1, $gamma1 );
        }
        $result1 = $result + 1000;
        error_log( 'beta1():$result: ' . $result . ' -> ' . $result1 );
        return $result1;
    };
}, 100 );

add_filter( 'alpha_beta', function( $beta ) {
    error_log( 'installing beta2():$beta = ' . print_r( $beta, true ) );
    $inner_beta2 = $beta;
    return function( $_this2, $gamma ) use ( $inner_beta2 ) {
        $gamma2 = $gamma + 10;
        error_log( 'beta2():$gamma: ' . $gamma . ' -> ' . $gamma2 );
        $_this2->epsilon += 1;
        error_log( 'beta2():$_this2 = ' . print_r( $_this2, true ) );
        error_log( 'beta2():$inner_beta2 = ' . print_r( $inner_beta2, true ) );
        if ( is_array( $inner_beta2 ) ) {
            $result = call_user_func( $inner_beta2,         $gamma2 );
        } else {
            $result = call_user_func( $inner_beta2, $_this2, $gamma2 );
        }
        $result2 = $result + 10000;
        error_log( 'beta2():$result: ' . $result . ' -> ' . $result2 );
        return $result2;
    };
}, 200 );

add_action( 'init', function() {

    error_log( '#######################################################################################' );
    error_log( 'omega( 0 ) = ' . omega( 0 ) );
    error_log( '#######################################################################################' );
    $alpha  = new Alpha();
    error_log( '$alpha->beta( 0 ) = ' . $alpha->beta( 0 ) );
    error_log( '#######################################################################################' . "\n\n\n\n\n" );
} );

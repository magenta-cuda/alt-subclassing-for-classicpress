<?php
/*
 * Plugin Name: Alt Subclassing for ClassicPress Experiment
 * Description: Proof of concept for ClassicPress issue #749.
 * Author: Magenta Cuda
 */

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
    error_log( 'installing beta1()' );
    $inner_beta1 = $beta;
    return function( $_this1, $gamma1 )  use ( $inner_beta1 ) {
        error_log( 'beta1():$gamma1 = ' . $gamma1 );
        ++$_this1->epsilon;
        error_log( 'beta1():$_this1 = ' . print_r( $_this1, true ) );
        $gamma1 += 100;
        error_log( 'beta1():$inner_beta1 = ' . print_r( $inner_beta1, true ) );
        if ( is_array( $inner_beta1 ) ) {
            $result = call_user_func( $inner_beta1,         $gamma1 );
        } else {
            $result = call_user_func( $inner_beta1, $_this1, $gamma1 );
        }
        $result += 1000;
        error_log( 'beta1():$result = ' . $result );
        return $result;
    };
}, 100 );

add_filter( 'alpha_beta', function( $beta ) {
    error_log( 'installing beta2()' );
    $inner_beta2 = $beta;
    return function( $_this2, $gamma2 ) use ( $inner_beta2 ) {
        error_log( 'beta2():$gamma2 = ' . $gamma2 );
        $_this2->epsilon += 1;
        error_log( 'beta2():$_this2 = ' . print_r( $_this2, true ) );
        $gamma2 += 10;
        error_log( 'beta2():$inner_beta2 = ' . print_r( $inner_beta2, true ) );
        if ( is_array( $inner_beta2 ) ) {
            $result = call_user_func( $inner_beta2,         $gamma2 );
        } else {
            $result = call_user_func( $inner_beta2, $_this2, $gamma2 );
        }
        $result += 10000;
        error_log( 'beta2():$result = ' . $result );
        return $result;
    };
}, 200 );

add_action( 'init', function() {

    error_log( '#######################################################################################' );
    $alpha  = new Alpha();
    $result = $alpha->beta( 0 );
    error_log( '#######################################################################################' . "\n\n\n\n\n" );
} );

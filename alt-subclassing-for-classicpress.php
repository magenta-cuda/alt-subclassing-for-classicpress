<?php
/*
 * Plugin Name: Alt Subclassing for ClassicPress Experiment
 * Description: Proof of concept for ClassicPress issue #749.
 * Author: Magenta Cuda
 */

class Alpha {
    var $epsilon = 0;

    public function beta( $gamma ) {
        error_log( 'beta():$gamma = ' . $gamma );
        $delta = apply_filters( 'alpha_beta', [ $this, 'beta0' ] );
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
    $inner_beta = $beta;
    return function( $_this, $gamma )  use ( $inner_beta ) {
        error_log( 'beta1():$gamma = ' . $gamma );
        ++$_this->epsilon;
        error_log( 'beta1():$_this = ' . print_r( $_this, true ) );
        $gamma += 100;
        if ( is_array( $inner_beta ) ) {
            $result = call_user_func( $inner_beta,         $gamma );
        } else {
            $result = call_user_func( $inner_beta, $_this, $gamma );
        }
        $result += 1000;
        error_log( 'beta1():$result = ' . $result );
        return $result;
    };
}, 100 );

add_filter( 'alpha_beta', function( $beta ) {
    error_log( 'installing beta2()' );
    $inner_beta = $beta;
    return function( $_this, $gamma ) use ( $inner_beta ) {
        error_log( 'beta2():$gamma = ' . $gamma );
        $_this->epsilon += 1;
        error_log( 'beta2():$_this = ' . print_r( $_this, true ) );
        $gamma += 10;
        if ( is_array( $inner_beta ) ) {
            $result = call_user_func( $inner_beta,         $gamma );
        } else {
            $result = call_user_func( $inner_beta, $_this, $gamma );
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

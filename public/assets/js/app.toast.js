'use strict';
/** 
 * Customer toaster methods
 */
var _position = 'top-right';
var _showHideTransition = 'slide';
var _stack = 4;

var _toast = {
    success: function (msg) {
        $.toast({
            heading: 'Success',
            icon: 'success',
            text: msg,
            position: _position,
            showHideTransition: _showHideTransition,
            stack: _stack
        })
    },
    error: function (msg) {
        $.toast({
            heading: 'Error',
            icon: 'error',
            text: msg,
            position: _position,
            showHideTransition: _showHideTransition,
            stack: _stack
        })
    },
    warning: function (msg) {
        $.toast({
            heading: 'Warning',
            icon: 'warning',
            text: msg,
            position: _position,
            showHideTransition: _showHideTransition,
            stack: _stack
        })
    },
    info: function (msg) {
        $.toast({
            heading: 'Information',
            icon: 'info',
            text: msg,
            position: _position,
            showHideTransition: _showHideTransition,
            stack: _stack
        })
    },
    successSticky: function (msg) {
        $.toast({
            heading: 'Success',
            icon: 'success',
            text: msg,
            position: _position,
            showHideTransition: _showHideTransition,
            stack: _stack,
            hideAfter: false,
        })
    },

    // Custom success with orange background, 3 seconds duration, and font size 18
    customSuccess: function (msg) {
        $.toast({
            heading: 'Success',
            icon: 'success',
            text: msg,
            position: _position,
            showHideTransition: _showHideTransition,
            stack: _stack,
            loader: false,    // Optional: disable the loader
            hideAfter: 3000, // Display for 3 seconds
            bgColor: '#F68922', // Set background to orange
            text: `<span style="font-size: 12px;">${msg}</span>` // Set font size to 18px
        });
    },
}
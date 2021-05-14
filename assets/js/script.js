/**
 * WP Theme Plugin Switcher admin settings
 *
 * @package Easy_Theme_Plugin_Switcher
 * @since  1.0.0
 */

(function( $ ) {

	/**
	 * WP Theme Plugin Switcher Admin JS
	 *
	 * @since 1.0.0
	 */
	EasyWPTPSwitcherAdmin = {

		init: function() {
            $( document ).delegate( ".load_switcher", "click", EasyWPTPSwitcherAdmin.open );
            $( document ).delegate( ".wp-switcher-sidenav-opened", "click", EasyWPTPSwitcherAdmin.close );
			$( document ).delegate( ".asstes-tab", "click", EasyWPTPSwitcherAdmin.toggle_tab );

			this._bind();
		},

		/**
		 * Binds events for the Astra Theme.
		 *
		 * @since 1.0.0
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
            // Theme events.
            $( document ).on( 'click', '.active-theme' , EasyWPTPSwitcherAdmin._activateTheme );

            // Plugin events.
			$( document ).on( 'click', '.activate-plugin' , EasyWPTPSwitcherAdmin._activatePlugin );
            $( document ).on( 'click', '.deactivate-plugin' , EasyWPTPSwitcherAdmin._deactivatePlugin );

            // Selected Plugins events.
            $( document ).on( 'click', '.selected-activate-plugins' , EasyWPTPSwitcherAdmin._selectedActivatePlugins );
            $( document ).on( 'click', '.selected-deactivate-plugins' , EasyWPTPSwitcherAdmin._selectedDeactivatePlugins );

            // All Plugin events.
            $( document ).on( 'click', '.all-activate-plugins' , EasyWPTPSwitcherAdmin._allActivatePlugins );
            $( document ).on( 'click', '.all-deactivate-plugins' , EasyWPTPSwitcherAdmin._allDeactivatePlugins );
        },

        /**
		 * Activate Theme Case
		 */
		_activateTheme: function( event, response ) {

            event.preventDefault();

            var message = jQuery(event.target);
            var themeStylesheeet = message.data('stylesheet');

			// Transform the 'Activate' button into an 'Deactivate' button.
			var activatingText = SwitcherLocalizer.switcher_activating_text;
            var switcherNonce = SwitcherLocalizer.switcher_nonce;
            var failedText = SwitcherLocalizer.switcher_failed_text;

            message.removeClass( 'button-disabled updated-message' )
				.addClass('updating-message')
				.text( activatingText );

			// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
			setTimeout( function() {

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						'action'            : 'easy_wp_theme_activate',
						'nonce'             : switcherNonce,
						'init'              : themeStylesheeet,
					},
                })
                .done(function (result) {

					if( result.success ) {
						message.removeClass( 'button-disabled updated-message' );
						location.reload();
					} else {
						message.removeClass( 'updating-message' );
						message.text( failedText );
					}
				});

			}, 1200 );
		},

		/**
		 * Activate Plugin Case
		 */
		_activatePlugin: function( event, response ) {

            event.preventDefault();

            var message = jQuery(event.target);
            var plugingSlug = message.data('slug');

			// Transform the 'Activate' button into an 'Deactivate' button.
			var activatingText = SwitcherLocalizer.switcher_activating_text;
			var deactivateText = SwitcherLocalizer.switcher_deactivate_text;
            var switcherNonce = SwitcherLocalizer.switcher_nonce;
            var failedText = SwitcherLocalizer.switcher_failed_text;

            message.removeClass( 'button-disabled updated-message' )
				.addClass('updating-message')
				.text( activatingText );

			// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
			setTimeout( function() {

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						'action'            : 'easy_wp_plugin_activate',
						'nonce'             : switcherNonce,
						'init'              : plugingSlug,
					},
                })
                .done(function (result) {

					if( result.success ) {
						message.removeClass( 'button-disabled updated-message' );
                        message.text( deactivateText );
						location.reload();
					} else {
						message.removeClass( 'updating-message' );
						message.text( failedText );
					}
				});

			}, 1200 );
		},

		/**
		 * Deactivate Plugin Case
		 */
		_deactivatePlugin: function( event, response ) {

			event.preventDefault();

			var message = jQuery(event.target);

			var plugingSlug = message.data('slug');

			// Transform the 'Deactivate' button into an 'Activate' button.
			var deactivatingText = SwitcherLocalizer.switcher_deactivating_text;
			var activateText = SwitcherLocalizer.switcher_activate_text;
			var failedText = SwitcherLocalizer.switcher_failed_text;
			var switcherNonce = SwitcherLocalizer.switcher_nonce;

			message.removeClass( 'button-disabled updated-message' )
				.addClass('updating-message')
				.text( deactivatingText );

			// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
			setTimeout( function() {

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						'action'            : 'easy_wp_plugin_deactivate',
						'nonce'             : switcherNonce,
						'init'              : plugingSlug,
					},
				})
				.done(function (result) {

					if( result.success ) {
						message.removeClass( 'button-disabled updated-message' );
                        message.text( activateText );
						location.reload();
					} else {
						message.removeClass( 'updating-message' );
						message.text( failedText );
					}
				});

			}, 1200 );
        },

        /**
		 * Selected Activate Plugins Case
		 */
		_selectedActivatePlugins: function( event, response ) {

            event.preventDefault();

            var message = jQuery(event.target);
            var selectedPlugins = [];

            $( '.wp-single-plugin input:checked' ).each( function() {
                selectedPlugins.push( $( this ).data( 'slug' ) );
            } );

			var activatingText = SwitcherLocalizer.switcher_activating_text;
			var activateText = SwitcherLocalizer.switcher_selected_activate_text;
            var switcherNonce = SwitcherLocalizer.switcher_nonce;
            var failedText = SwitcherLocalizer.switcher_failed_text;

            // WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
            message.removeClass( 'button-disabled updated-message' )
				.addClass('updating-message')
				.text( activatingText );

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    'action'            : 'easy_wp_selected_plugins_activate',
                    'nonce'             : switcherNonce,
                    'init'              : selectedPlugins,
                },
            })
            .done(function (result) {

                if( result.success ) {
                    message.removeClass( 'button-disabled updated-message' );
                    message.text( activateText );
                    location.reload();
                } else {
                    message.removeClass( 'updating-message' );
                    message.text( failedText );
                }
            });
        },

        /**
		 * Selected Activate Plugins Case
		 */
		_selectedDeactivatePlugins: function( event, response ) {

            event.preventDefault();

            var message = jQuery(event.target);
            var selectedPlugins = [];

            $( '.wp-single-plugin input:checked' ).each( function() {
                selectedPlugins.push( $( this ).data( 'slug' ) );
            } );

			// Transform the 'Activate' button into an 'Deactivate' button.
			var deactivatingText = SwitcherLocalizer.switcher_deactivating_text;
			var deactivateText = SwitcherLocalizer.switcher_selected_deactivate_text;
            var switcherNonce = SwitcherLocalizer.switcher_nonce;
            var failedText = SwitcherLocalizer.switcher_failed_text;

            // WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
            message.removeClass( 'button-disabled updated-message' )
				.addClass('updating-message')
				.text( deactivatingText );

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    'action'            : 'easy_wp_selected_plugins_deactivate',
                    'nonce'             : switcherNonce,
                    'init'              : selectedPlugins,
                },
            })
            .done(function (result) {

                if( result.success ) {
                    message.removeClass( 'button-disabled updated-message' );
                    message.text( deactivateText );
                    location.reload();
                } else {
                    message.removeClass( 'updating-message' );
                    message.text( failedText );
                }
            });
        },
        
        /**
		 * All Activate Plugins Case
		 */
		_allActivatePlugins: function( event, response ) {

            event.preventDefault();

            var message = jQuery(event.target);

			// Transform the 'Activate' button into an 'Deactivate' button.
			var activatingText = SwitcherLocalizer.switcher_activating_text;
			var deactivateText = SwitcherLocalizer.switcher_all_deactivate_text;
            var switcherNonce = SwitcherLocalizer.switcher_nonce;
            var failedText = SwitcherLocalizer.switcher_failed_text;

            // WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
            message.removeClass( 'button-disabled updated-message' )
				.addClass('updating-message')
				.text( activatingText );

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    'action'            : 'easy_wp_all_plugins_activate',
                    'nonce'             : switcherNonce,
                },
            })
            .done(function (result) {

                if( result.success ) {
                    message.removeClass( 'button-disabled updated-message' );
                    message.text( deactivateText );
                    location.reload();
                } else {
                    message.removeClass( 'updating-message' );
                    message.text( failedText );
                }
            });
        },

        /**
		 * All Activate Plugins Case
		 */
		_allDeactivatePlugins: function( event, response ) {

            event.preventDefault();

            var message = jQuery(event.target);

			// Transform the 'Activate' button into an 'Deactivate' button.
			var deactivatingText = SwitcherLocalizer.switcher_deactivating_text;
			var deactivateText = SwitcherLocalizer.switcher_all_deactivate_text;
            var switcherNonce = SwitcherLocalizer.switcher_nonce;
            var failedText = SwitcherLocalizer.switcher_failed_text;

            // WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
            message.removeClass( 'button-disabled updated-message' )
				.addClass('updating-message')
				.text( deactivatingText );

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    'action'            : 'easy_wp_all_plugins_deactivate',
                    'nonce'             : switcherNonce,
                },
            })
            .done(function (result) {

                if( result.success ) {
                    message.removeClass( 'button-disabled updated-message' );
                    message.text( deactivateText );
                    location.reload();
                } else {
                    message.removeClass( 'updating-message' );
                    message.text( failedText );
                }
            });
		},

        /**
		 * Toggle.
		 */
		toggle_tab: function( e ) {

			e.stopPropagation();
            e.preventDefault();

            $this 		= $(this),
                wrapper 	= $this.data('wrapper'),
                content_wrapper = $this.closest('#wp-switcher-sidenav').find( '.' + wrapper );

            $this.siblings().removeClass('active-tab');
            $this.addClass('active-tab');

            if( $( content_wrapper ).length ) {
                $( content_wrapper ).siblings().removeClass('active-asset');
                $( content_wrapper ).addClass('active-asset');
            }
        },

		/**
		 * Open.
		 */
		open: function( e ) {

			e.stopPropagation();
			e.preventDefault();

            if( $(this).hasClass( 'wp-switcher-sidenav-opened' ) ) {
                $(this).removeClass( 'wp-switcher-sidenav-opened' );
                $( this ).text('☰');
                $(this).addClass( 'wp-switcher-sidenav-closed' );
            } else {
                $(this).removeClass( 'wp-switcher-sidenav-closed' );
                $( this ).text('✕');
                $(this).addClass( 'wp-switcher-sidenav-opened' );
            }

            $( '#wp-switcher-sidenav' ).css('width', '320px');
            $( '#wp-switcher-toggle-wrap' ).css('margin-left', '320px');
        },

        /**
		 * Close.
		 */
		close: function( e ) {

			e.stopPropagation();
			e.preventDefault();

            // $( '#wp-switcher-toggle-wrap .load_switcher' ).css('right', '-10px');

            $( '#wp-switcher-sidenav' ).css('width', '0px');
            $( '#wp-switcher-toggle-wrap' ).css('margin-left', '0px');
		},
	}

	$( document ).ready(function() {
		EasyWPTPSwitcherAdmin.init();
	});

})( jQuery );

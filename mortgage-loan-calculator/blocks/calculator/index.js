/* Editor side of a dynamic block: the markup is rendered by PHP (the same callback as the [mlcalc] shortcode), the
   editor shows it through ServerSideRender and edits the attributes in the sidebar. No build step: plain wp.* globals. */
( function ( blocks, element, blockEditor, components, i18n, serverSideRender ) {
	var el = element.createElement;
	var __ = i18n.__;

	function select( props, key, label, options ) {
		return el( components.SelectControl, {
			label: label,
			value: props.attributes[ key ],
			options: options,
			onChange: function ( v ) {
				var o = {};
				o[ key ] = v;
				props.setAttributes( o );
			},
		} );
	}

	function text( props, key, label, help ) {
		return el( components.TextControl, {
			label: label,
			help: help,
			value: props.attributes[ key ],
			onChange: function ( v ) {
				var o = {};
				o[ key ] = v;
				props.setAttributes( o );
			},
		} );
	}

	blocks.registerBlockType( 'mortgage-loan-calculator/calculator', {
		edit: function ( props ) {
			var blank = __( 'Leave empty for the default', 'mortgage-loan-calculator' );
			return el(
				'div',
				blockEditor.useBlockProps(),
				el(
					blockEditor.InspectorControls,
					null,
					el(
						components.PanelBody,
						{ title: __( 'Calculator', 'mortgage-loan-calculator' ) },
						select( props, 'default', __( 'Calculators', 'mortgage-loan-calculator' ), [
							{ value: 'mortgage_only', label: __( 'Mortgage only', 'mortgage-loan-calculator' ) },
							{ value: 'mortgage', label: __( 'Mortgage by default', 'mortgage-loan-calculator' ) },
							{ value: 'loan_only', label: __( 'Loan only', 'mortgage-loan-calculator' ) },
							{ value: 'loan', label: __( 'Loan by default', 'mortgage-loan-calculator' ) },
						] ),
						select( props, 'size', __( 'Form size', 'mortgage-loan-calculator' ), [
							{ value: 'wide', label: __( 'Wide (300px)', 'mortgage-loan-calculator' ) },
							{ value: 'narrow', label: __( 'Narrow (150px)', 'mortgage-loan-calculator' ) },
						] ),
						select( props, 'currency', __( 'Currency', 'mortgage-loan-calculator' ), [
							{ value: 'usd', label: 'USD ($)' },
							{ value: 'eur', label: 'EUR (€)' },
							{ value: 'gbp', label: 'GBP (£)' },
						] ),
						select( props, 'language', __( 'Language', 'mortgage-loan-calculator' ), [
							{ value: '', label: __( 'Site language', 'mortgage-loan-calculator' ) },
							{ value: 'en', label: 'English' },
							{ value: 'de', label: 'Deutsch' },
							{ value: 'es', label: 'Español' },
							{ value: 'fr', label: 'Français' },
							{ value: 'it', label: 'Italiana' },
							{ value: 'pt', label: 'Português' },
							{ value: 'ru', label: 'Русский' },
							{ value: 'al', label: 'Shqip' },
						] ),
						select( props, 'schedule', __( 'Amortization schedule', 'mortgage-loan-calculator' ), [
							{ value: 'year_nc', label: __( 'By year (no selector)', 'mortgage-loan-calculator' ) },
							{ value: 'month_nc', label: __( 'By month (no selector)', 'mortgage-loan-calculator' ) },
							{ value: 'year', label: __( 'By year', 'mortgage-loan-calculator' ) },
							{ value: 'month', label: __( 'By month', 'mortgage-loan-calculator' ) },
							{ value: 'none', label: __( 'Do not show', 'mortgage-loan-calculator' ) },
						] )
					),
					el(
						components.PanelBody,
						{ title: __( 'Default values', 'mortgage-loan-calculator' ), initialOpen: false },
						text( props, 'price', __( 'Purchase price', 'mortgage-loan-calculator' ), blank ),
						text( props, 'down', __( 'Down payment, %', 'mortgage-loan-calculator' ), blank ),
						text( props, 'mterm', __( 'Mortgage term, years', 'mortgage-loan-calculator' ), blank ),
						text( props, 'rate', __( 'Interest rate, %', 'mortgage-loan-calculator' ), blank ),
						text( props, 'tax', __( 'Property tax per year', 'mortgage-loan-calculator' ), blank ),
						text( props, 'insurance', __( 'Property insurance per year', 'mortgage-loan-calculator' ), blank ),
						text( props, 'pmi', __( 'PMI, %', 'mortgage-loan-calculator' ), blank ),
						text( props, 'amount', __( 'Loan amount', 'mortgage-loan-calculator' ), blank ),
						text( props, 'lterm', __( 'Loan term, years', 'mortgage-loan-calculator' ), blank )
					)
				),
				el( serverSideRender, { block: 'mortgage-loan-calculator/calculator', attributes: props.attributes } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n, window.wp.serverSideRender );

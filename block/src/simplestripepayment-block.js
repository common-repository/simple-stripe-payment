const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { SelectControl, TextControl, PanelBody } = wp.components;
const { serverSideRender: ServerSideRender } = wp;

registerBlockType(
	'simple-stripe-payment/simplestripepayment-block',
	{
		title: 'Simple Stripe Payment',
		icon: 'tickets-alt',
		category: 'widgets',

		edit ( props ) {

			return [
			<Fragment>
				<ServerSideRender
					block = 'simple-stripe-payment/simplestripepayment-block'
					attributes = { props.attributes }
				/>
				<TextControl
					label = { simplestripepayment_text.amount }
					value = { props.attributes.amount }
					onChange = { ( value ) => props.setAttributes( { amount: value } ) }
				/>
				<SelectControl
					label = { simplestripepayment_text.currency }
					value = { props.attributes.currency }
					options = { [
					{ value: 'USD', label: 'United States dollar' },
					{ value: 'AUD', label: 'Australian dollar' },
					{ value: 'BRL', label: 'Brazilian real' },
					{ value: 'CAD', label: 'Canadian dollar' },
					{ value: 'CZK', label: 'Czech koruna' },
					{ value: 'DKK', label: 'Danish krone' },
					{ value: 'EUR', label: 'Euro' },
					{ value: 'HKD', label: 'Hong Kong dollar' },
					{ value: 'HUF', label: 'Hungarian forint' },
					{ value: 'INR', label: 'Indian rupee' },
					{ value: 'ILS', label: 'Israeli new shekel' },
					{ value: 'JPY', label: 'Japanese yen' },
					{ value: 'MYR', label: 'Malaysian ringgit' },
					{ value: 'MXN', label: 'Mexican peso' },
					{ value: 'TWD', label: 'New Taiwan dollar' },
					{ value: 'NZD', label: 'New Zealand dollar' },
					{ value: 'NOK', label: 'Norwegian krone' },
					{ value: 'PHP', label: 'Philippine peso' },
					{ value: 'PLN', label: 'Polish zloty' },
					{ value: 'GBP', label: 'Pound sterling' },
					{ value: 'RUB', label: 'Russian ruble' },
					{ value: 'SGD', label: 'Singapore dollar' },
					{ value: 'SEK', label: 'Swedish krona' },
					{ value: 'CHF', label: 'Swiss franc' },
					{ value: 'THB', label: 'Thai baht' },
					] }
					onChange = { ( value ) => props.setAttributes( { currency: value } ) }
				/>
				<div>{ simplestripepayment_text.check }</div>

				<InspectorControls>
				{}
					<PanelBody title = { simplestripepayment_text.amount } initialOpen = { false }>
						<TextControl
							label = { simplestripepayment_text.amount }
							value = { props.attributes.amount }
							onChange = { ( value ) => props.setAttributes( { amount: value } ) }
						/>
					</PanelBody>
					<PanelBody title = { simplestripepayment_text.name } initialOpen = { false }>
						<TextControl
							label = { simplestripepayment_text.name }
							value = { props.attributes.name }
							onChange = { ( value ) => props.setAttributes( { name: value } ) }
						/>
						<TextControl
							label = { simplestripepayment_text.description }
							value = { props.attributes.description }
							onChange = { ( value ) => props.setAttributes( { description: value } ) }
						/>
					</PanelBody>
					<PanelBody title = { simplestripepayment_text.currency } initialOpen = { false }>
						<SelectControl
							label = { simplestripepayment_text.currency }
							value = { props.attributes.currency }
							options = { [
							{ value: 'USD', label: 'United States dollar' },
							{ value: 'AUD', label: 'Australian dollar' },
							{ value: 'BRL', label: 'Brazilian real' },
							{ value: 'CAD', label: 'Canadian dollar' },
							{ value: 'CZK', label: 'Czech koruna' },
							{ value: 'DKK', label: 'Danish krone' },
							{ value: 'EUR', label: 'Euro' },
							{ value: 'HKD', label: 'Hong Kong dollar' },
							{ value: 'HUF', label: 'Hungarian forint' },
							{ value: 'INR', label: 'Indian rupee' },
							{ value: 'ILS', label: 'Israeli new shekel' },
							{ value: 'JPY', label: 'Japanese yen' },
							{ value: 'MYR', label: 'Malaysian ringgit' },
							{ value: 'MXN', label: 'Mexican peso' },
							{ value: 'TWD', label: 'New Taiwan dollar' },
							{ value: 'NZD', label: 'New Zealand dollar' },
							{ value: 'NOK', label: 'Norwegian krone' },
							{ value: 'PHP', label: 'Philippine peso' },
							{ value: 'PLN', label: 'Polish zloty' },
							{ value: 'GBP', label: 'Pound sterling' },
							{ value: 'RUB', label: 'Russian ruble' },
							{ value: 'SGD', label: 'Singapore dollar' },
							{ value: 'SEK', label: 'Swedish krona' },
							{ value: 'CHF', label: 'Swiss franc' },
							{ value: 'THB', label: 'Thai baht' },
							] }
							onChange = { ( value ) => props.setAttributes( { currency: value } ) }
						/>
					</PanelBody>
					<PanelBody title = { simplestripepayment_text.view } initialOpen = { false }>
						<TextControl
							label = { simplestripepayment_text.label }
							value = { props.attributes.label }
							onChange = { ( value ) => props.setAttributes( { label: value } ) }
						/>
						<TextControl
							label = { simplestripepayment_text.before }
							value = { props.attributes.before }
							onChange = { ( value ) => props.setAttributes( { before: value } ) }
						/>
						<TextControl
							label = { simplestripepayment_text.after }
							value = { props.attributes.after }
							onChange = { ( value ) => props.setAttributes( { after: value } ) }
						/>
						<TextControl
							label = { simplestripepayment_text.remove }
							value = { props.attributes.remove }
							onChange = { ( value ) => props.setAttributes( { remove: value } ) }
						/>
					</PanelBody>
					<PanelBody title = { simplestripepayment_text.payname } initialOpen = { false }>
						<TextControl
							label = { simplestripepayment_text.payname }
							value = { props.attributes.payname }
							onChange = { ( value ) => props.setAttributes( { payname: value } ) }
						/>
					</PanelBody>
				</InspectorControls>
			</Fragment>
			];
		},

		save () {
			return null;
		},

	}
);

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Import necessary UI Elements
 */
import { SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit(props) {
	
	const {
		attributes: { id },
		setAttributes,
		className,
	} = props;

	let id_options = [];

	//add empty option
	id_options.push({
		value: '',
		label: ''
	});

	// Request data
	const data = useSelect((select) => {
		return select('core').getEntityRecords('postType', 'chps-content-hub');
	});

	// Has the request resolved?
	const isLoading = useSelect((select) => {
			return select('core/data').isResolving('core', 'getEntityRecords', [
					'postType', 'chps-content-hub'
			]);
	});

	// Show the loading state if we're still waiting.
	if (isLoading) {
		return (
			<div { ...useBlockProps() }>
				<h3>Loading...</h3>
			</div>
		)
	}
	
	// continue here when data is fetched
	if(data != null) {
		data.forEach((el) => {
			id_options.push(
				{
					value: el.post_meta_fields.ch_id, 
					label: el.title.rendered
				}
			)
		})
	}

	return (
		<div { ...useBlockProps() }> 

			<div class="chps_logo"></div>

			<SelectControl
				label="Select a content hub"
				value={ id }
				options={ id_options }
				onChange={ 
					( selectedId ) => {
						setAttributes( { id: Number(selectedId) } );
					}
				}
			/>
		</div>
	);
}

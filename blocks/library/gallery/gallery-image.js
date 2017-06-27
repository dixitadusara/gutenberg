
export default function GalleryImage( props ) {
	return (
		<figure className="blocks-gallery-image">
			<img src={ props.img.url } alt={ props.img.alt } data-wp-media-id={ props.img.id } />
		</figure>
	);
}

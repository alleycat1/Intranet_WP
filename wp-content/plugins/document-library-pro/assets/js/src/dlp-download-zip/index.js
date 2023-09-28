import { zip } from 'fflate';
import { saveAs } from 'file-saver';
import fromEntries from 'core-js/features/object/from-entries'; // polyfilled for Edge Legacy

/**
 * Zips all provided URLs and triggers a browser download
 *
 * @param {array} urls
 * @param {string} filename
 */
function getZipFile( urls, filename ) {
	if ( urls.length < 1 ) {
		return;
	}

	const zipPromise = new Promise( ( resolve, reject ) => {
		Promise.all(
			urls.map( ( url ) =>
				fetch( url )
					.then( ( response ) => {
						if ( response.status === 200 || response.status === 0 ) {
							return Promise.resolve( response.arrayBuffer() );
						} else {
							return Promise.reject(
								new Error( response.statusText )
							);
						}
					} )
					.then( ( arrayBuffer ) => {
						return [
							url.split( '/' ).pop(),
							new Uint8Array( arrayBuffer ),
						];
					} )
					.catch( ( error ) => {
						throw error;
					} )
			)
		)
			.then( ( fileArrayBuffers ) => {
				const zipObject = fromEntries( new Map( fileArrayBuffers ) );

				zip( zipObject, { level: 0 }, ( error, out ) => {
					if ( error ) {
						throw error;
					} else {
						saveAs( new Blob( [ out ] ), filename );
						resolve( true );
					}
				} );
			} )
			.catch( ( error ) => {
				console.log( 'zip error', error );
				reject( error );
			} );
	} );

	return zipPromise;
}

window.getZipFile = getZipFile;

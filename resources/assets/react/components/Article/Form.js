import React from 'react';
import { Link } from 'react-router-dom';

import SimpleMDE from 'simplemde';
import Editor from 'react-simplemde-editor';
import "simplemde/dist/simplemde.min.css";

import { getTime } from '../../utils.js';

class ArticleForm extends React.Component {
	render() {
		return (
			<Editor
				onChange={ this.handleChange }
				options={{
					spellChecker: false,
					toolbar: [
		                {
		                    name: "bold",
		                    action: SimpleMDE.toggleBold,
		                    className: "fas fa-fw fa-bold",
		                    title: "Gras",
		                },
		                {
		                    name: "italic",
		                    action: SimpleMDE.toggleItalic,
		                    className: "fas fa-fw fa-italic",
		                    title: "Italique",
		                },
		                {
		                    name: "heading",
		                    action: SimpleMDE.toggleHeadingSmaller,
		                    className: "fas fa-fw fa-heading",
		                    title: "Titre",
		                },
		                {
		                    name: "quote",
		                    action: SimpleMDE.toggleBlockquote,
		                    className: "fas fa-fw fa-quote-left",
		                    title: "Citation",
		                },
		                {
		                    name: "unordered-list",
		                    action: SimpleMDE.toggleUnorderedList,
		                    className: "fas fa-fw fa-list-ul",
		                    title: "Liste non-ordonnée",
		                },
		                {
		                    name: "ordered-list",
		                    action: SimpleMDE.toggleOrderedList,
		                    className: "fas fa-fw fa-list-ol",
		                    title: "Liste ordonnée",
		                },
		                {
		                    name: "link",
		                    action: SimpleMDE.drawLink,
		                    className: "fas fa-fw fa-link",
		                    title: "Insérer un lien",
		                },
		                {
		                    name: "image",
		                    action: SimpleMDE.drawImage,
		                    className: "far fa-fw fa-image",
		                    title: "Insérer une image",
		                },
		                {
		                    name: "table",
		                    action: SimpleMDE.drawTable,
		                    className: "fas fa-fw fa-table",
		                    title: "Insérer un tableau",
		                },
		                {
		                    name: "preview",
		                    action: SimpleMDE.togglePreview,
		                    className: "fas fa-fw fa-eye no-disable",
		                    title: "Aperçu",
		                },
		                {
		                    name: "side-by-side",
		                    action: SimpleMDE.toggleSideBySide,
		                    className: "fas fa-fw fa-columns no-disable no-mobile",
		                    title: "Cote à cote",
		                },
		                {
		                    name: "fullscreen",
		                    action: SimpleMDE.toggleFullScreen,
		                    className: "fas fa-fw fa-arrows-alt no-disable no-mobile",
		                    title: "Plein écran",
		                }
		            ]
				}}
			/>
		);
	}
}

export default ArticleForm;
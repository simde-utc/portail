import React from 'react';
import { Link } from 'react-router-dom';

import SimpleMDE from 'react-simplemde-editor';
import "simplemde/dist/simplemde.min.css";

import { getTime } from '../../utils.js';

class ArticleForm extends React.Component {
	render() {
		const smdeInstance = require("simplemde");

		return (
			<SimpleMDE
				onChange={this.handleChange}
				options={{
					spellChecker: false,
					toolbar: [
		                {
		                    name: "bold",
		                    action: smdeInstance.toggleBold,
		                    className: "fas fa-fw fa-bold",
		                    title: "Gras",
		                },
		                {
		                    name: "italic",
		                    action: smdeInstance.toggleItalic,
		                    className: "fas fa-fw fa-italic",
		                    title: "Italique",
		                },
		                {
		                    name: "heading",
		                    action: smdeInstance.toggleHeadingSmaller,
		                    className: "fas fa-fw fa-heading",
		                    title: "Titre",
		                },
		                {
		                    name: "quote",
		                    action: smdeInstance.toggleBlockquote,
		                    className: "fas fa-fw fa-quote-left",
		                    title: "Citation",
		                },
		                {
		                    name: "unordered-list",
		                    action: smdeInstance.toggleUnorderedList,
		                    className: "fas fa-fw fa-list-ul",
		                    title: "Liste non-ordonnée",
		                },
		                {
		                    name: "ordered-list",
		                    action: smdeInstance.toggleOrderedList,
		                    className: "fas fa-fw fa-list-ol",
		                    title: "Liste ordonnée",
		                },
		                {
		                    name: "link",
		                    action: smdeInstance.drawLink,
		                    className: "fas fa-fw fa-link",
		                    title: "Insérer un lien",
		                },
		                {
		                    name: "image",
		                    action: smdeInstance.drawImage,
		                    className: "far fa-fw fa-image",
		                    title: "Insérer une image",
		                },
		                {
		                    name: "table",
		                    action: smdeInstance.drawTable,
		                    className: "fas fa-fw fa-table",
		                    title: "Insérer un tableau",
		                },
		                {
		                    name: "preview",
		                    action: smdeInstance.togglePreview,
		                    className: "fas fa-fw fa-eye no-disable",
		                    title: "Aperçu",
		                },
		                {
		                    name: "side-by-side",
		                    action: smdeInstance.toggleSideBySide,
		                    className: "fas fa-fw fa-columns no-disable no-mobile",
		                    title: "Cote à cote",
		                },
		                {
		                    name: "fullscreen",
		                    action: smdeInstance.toggleFullScreen,
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
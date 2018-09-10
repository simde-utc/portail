import React from 'react';
import { Link } from 'react-router-dom';

import SimpleMDE from 'simplemde';
import Editor from 'react-simplemde-editor';
import "simplemde/dist/simplemde.min.css";

import { getTime } from '../../utils.js';

class ArticleForm extends React.Component {
	constructor() {
		super();

		this.state = {
			form: {
				title: "Hey",
				description: "Hello",
				content: "Hi"
			}
		};
	}

	handleEditorChange(value) {
		var form = this.state.form;
		form.content = value;
		this.setState({ form: form });
	}

	handleChange(e) {
		var form = this.state.form;
		form[e.target.name] = e.target.value;
        this.setState({ form: form });
    }

    handleSubmit(e) {
        e.preventDefault();

        this.props.post(this.state.form);
    }

	render() {
		return (
			<div>
				<div className="container p-3">
					<form className="form row" onSubmit={ (e) => this.handleSubmit(e) }>
						<div className="col-md-6">
							<h2 className="mb-3">Créer un article</h2>
							<div className="form-group">
							    <label>Titre *</label>
							    <input 
							    	type="text" 
							    	className="form-control" 
							    	name="title" 
							    	value={ this.state.form.title }
							    	onChange={ (e) => this.handleChange(e) }
							    	placeholder="Entrez le titre de votre article"
							    	required
							    />
							</div>
							<div className="form-group">
							    <label>Description</label>
							    <textarea 
							    	className="form-control" 
							    	name="description" 
							    	rows="3"
							    	value={ this.state.form.description }
							    	onChange={ (e) => this.handleChange(e) }
							    	placeholder="Entrez une courte description de votre article"
							    >
							    </textarea>
							</div>
						</div>
						<div className="col-md-12">
							<div className="form-group">
							    <label>Corps *</label>
								<Editor
									onChange={ (e) => this.handleEditorChange(e) }
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
							</div>

							<button type="submit" className="btn btn-primary">
								Publier
							</button>
						</div>
					</form>
				</div>
			</div>
		);
	}
}

export default ArticleForm;
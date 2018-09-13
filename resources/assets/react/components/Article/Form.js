import React from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import { visibilitiesActions } from '../../redux/actions';

import SimpleMDE from 'simplemde';
import Editor from 'react-simplemde-editor';
import Select from 'react-select';
import { map } from 'lodash';
import "simplemde/dist/simplemde.min.css";

import { getTime } from '../../utils.js';

@connect(store => ({
	visibilities: store.visibilities.data,
}))
class ArticleForm extends React.Component {
	constructor() {
		super();

		this.state = {
			title: "",
			description: "",
			content: "",
		};
	}

	getVisibilities(visibilities) {
		return map(visibilities, (visibility => ({
			value: visibility.id,
			label: visibility.name
		})));
	}

	handleVisibilityChange(value) {
		this.setState(prevState => ({ ...prevState, visibility_id: value }));
	}

	componentWillMount() {
		this.props.dispatch(visibilitiesActions.getAll());
	}

	handleEditorChange(value) {
		this.setState(prevState => ({ ...prevState, content: value }));
	}

	handleChange(e) {
		e.persist();

		this.setState(prevState => ({ ...prevState, [e.target.name]: e.target.value }));
  }

  handleSubmit(e) {
    e.preventDefault();

    this.props.post({
			title: this.state.title,
			description: this.state.description,
			content: this.state.content,
			visibility_id: this.state.visibility_id,
		});
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
							    	value={ this.state.title }
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
							    	value={ this.state.description }
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

							<Select
								onChange={ this.handleVisibilityChange.bind(this) }
								name="visibility_id"
								placeholder="Visibilité de l'article"
								defaultValue={ this.props.visibilities[0] && this.props.visibilities[0].id }
								options={ this.getVisibilities(this.props.visibilities) }
							/>

							<Select
								onChange={ this.handleEventChange.bind(this) }
								name="event_id"
								placeholder="Evènement attaché"
								isSearchable={ true }
								onInputChange={ this.handleSearchEvent.bind(this) }
							/>

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

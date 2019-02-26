import React from 'react';
import { connect } from 'react-redux';

import SimpleMDE from 'simplemde';
import Editor from 'react-simplemde-editor';
import Select from 'react-select';
import { map } from 'lodash';
import 'simplemde/dist/simplemde.min.css';

@connect(store => ({
	visibilities: store.getData('visibilities'),
}))
class ArticleForm extends React.Component {
	static mapSelectionOptions(options) {
		return map(options, ({ id, name }) => ({
			value: id,
			label: name,
		}));
	}

	constructor(props) {
		super(props);

		this.state = {
			title: '',
			description: '',
			content: '',
			eventFilter: '',
		};
	}

	getEvents(events) {
		const { eventFilter } = this.state;

		return ArticleForm.mapSelectionOptions(
			events.filter(eventToFilter => {
				return eventToFilter.name.indexOf(eventFilter) >= 0;
			})
		);
	}

	handleVisibilityChange({ value }) {
		this.setState({ visibility_id: value });
	}

	handleSearchEvent(value) {
		this.setState({ eventFilter: value });
	}

	handleEventChange(value) {
		this.setState({ event_id: value.value });
	}

	handleEditorChange(value) {
		this.setState({ content: value });
	}

	handleChange(e) {
		e.persist();

		this.setState({ [e.target.name]: e.target.value });
	}

	handleSubmit(e) {
		const { title, description, content, visibility_id, event_id } = this.state;
		const { post } = this.props;
		e.preventDefault();

		post({
			title,
			description,
			content,
			visibility_id,
			event_id,
		});
	}

	render() {
		const { visibilities, events } = this.props;
		const { title, description } = this.state;
		console.log(visibilities)
		return (
			<div>
				<div className="container p-3">
					<form className="form row" onSubmit={e => this.handleSubmit(e)}>
						<div className="col-md-6">
							<h2 className="mb-3">Créer un article</h2>
							<div className="form-group">
								<label htmlFor="title">
									Titre *
									<input
										type="text"
										className="form-control"
										id="title"
										name="title"
										value={title}
										onChange={e => this.handleChange(e)}
										placeholder="Entrez le titre de votre article"
										required
									/>
								</label>
							</div>
							<div className="form-group">
								<label htmlFor="description">
									Description
									<textarea
										className="form-control"
										id="description"
										name="description"
										rows="3"
										value={description}
										onChange={e => this.handleChange(e)}
										placeholder="Entrez une courte description de votre article"
									/>
								</label>
							</div>
						</div>
						<div className="col-md-12">
							<div className="form-group">
								<label htmlFor="corps">
									Corps *
									<Editor
										onChange={e => this.handleEditorChange(e)}
										id="corps"
										options={{
											spellChecker: false,
											toolbar: [
												{
													name: 'bold',
													action: SimpleMDE.toggleBold,
													className: 'fas fa-fw fa-bold',
													title: 'Gras',
												},
												{
													name: 'italic',
													action: SimpleMDE.toggleItalic,
													className: 'fas fa-fw fa-italic',
													title: 'Italique',
												},
												{
													name: 'heading',
													action: SimpleMDE.toggleHeadingSmaller,
													className: 'fas fa-fw fa-heading',
													title: 'Titre',
												},
												{
													name: 'quote',
													action: SimpleMDE.toggleBlockquote,
													className: 'fas fa-fw fa-quote-left',
													title: 'Citation',
												},
												{
													name: 'unordered-list',
													action: SimpleMDE.toggleUnorderedList,
													className: 'fas fa-fw fa-list-ul',
													title: 'Liste non-ordonnée',
												},
												{
													name: 'ordered-list',
													action: SimpleMDE.toggleOrderedList,
													className: 'fas fa-fw fa-list-ol',
													title: 'Liste ordonnée',
												},
												{
													name: 'link',
													action: SimpleMDE.drawLink,
													className: 'fas fa-fw fa-link',
													title: 'Insérer un lien',
												},
												{
													name: 'image',
													action: SimpleMDE.drawImage,
													className: 'far fa-fw fa-image',
													title: 'Insérer une image',
												},
												{
													name: 'table',
													action: SimpleMDE.drawTable,
													className: 'fas fa-fw fa-table',
													title: 'Insérer un tableau',
												},
												{
													name: 'preview',
													action: SimpleMDE.togglePreview,
													className: 'fas fa-fw fa-eye no-disable',
													title: 'Aperçu',
												},
												{
													name: 'side-by-side',
													action: SimpleMDE.toggleSideBySide,
													className: 'fas fa-fw fa-columns no-disable no-mobile',
													title: 'Cote à cote',
												},
												{
													name: 'fullscreen',
													action: SimpleMDE.toggleFullScreen,
													className: 'fas fa-fw fa-arrows-alt no-disable no-mobile',
													title: 'Plein écran',
												},
											],
										}}
									/>
								</label>
							</div>

							<Select
								onChange={this.handleVisibilityChange.bind(this)}
								name="visibility_id"
								placeholder="Visibilité de l'article"
								options={ArticleForm.mapSelectionOptions(visibilities)}
							/>

							<br />

							<Select
								onChange={this.handleEventChange.bind(this)}
								name="event_id"
								placeholder="Evènement attaché"
								isSearchable
								onInputChange={this.handleSearchEvent.bind(this)}
								options={this.getEvents(events)}
							/>

							<br />

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

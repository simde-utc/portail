/**
 * Affiche les associations de l'utlisateur.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import Article from './Article';

const ArticleList = ({ articles, fetched }) => (
	<div className="container ArticleList">
		{fetched ? (
			articles.map(article => <Article key={article.id} article={article} />)
		) : (
			<div>Chargement</div>
		)}
	</div>
);

export default ArticleList;

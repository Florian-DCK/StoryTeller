function lazyLoadStories(url) {
    // console.log('Chargement des histoires...');
    const container = document.getElementById('stories-container');
    container.classList.add('loading');

    // Appel à l'API pour récupérer les histoires
    fetch(url)
        .then(response => response.json())
        .then(stories => {
            const container = document.getElementById('stories-container');
            
            Promise.all(stories.map(story =>
                Promise.all([
                    fetch(`/serve/users/${story.user_id}`).then(res => res.json()).then(data => data.username || 'Inconnu'),
                    fetch(`/serve/participations/${story.id}?limit=2`).then(res => res.json())
                        .then(participations => Promise.all(
                            participations.map(p => 
                                fetch(`/serve/users/${p.user_id}`)
                                    .then(res => res.json())
                                    .then(userData => {
                                        console.log('userData pour participation:', p.user_id, userData);
                                        return {
                                            content: p.content,
                                            author: userData.userName || 'Inconnu',
                                            avatar: userData.avatar
                                        };
                                    })
                            )
                        ).then(formattedParticipations => {
                            // On fait une requête supplémentaire pour connaître le nombre total de participations
                            return fetch(`/serve/participations/${story.id}`)
                                .then(res => res.json())
                                .then(allParticipations => ({
                                    participations: formattedParticipations,
                                    totalCount: allParticipations.length
                                }));
                        })
                    )
                ]).then(([author, participationsData]) => ({
                    id: story.id,
                    title: story.title,
                    author,
                    participationNumber: participationsData.totalCount,
                    likes: story.likes,
                    participations: participationsData.participations,
                    themes: story.themes || [], // Ajout des thèmes de l'histoire
                    full: participationsData.totalCount > 2 // true si plus de 2 participations
                }
            ))
            ))
            .then(formattedStories => {
                Promise.all([
                    fetch('/api/templates/storycard.mustache').then(response => response.text()),
                    fetch('/api/templates/partials/participation.mustache').then(response => response.text())
                ])
                .then(([templateText, participationTemplate]) => {
                    Mustache.parse(participationTemplate);
                    const partials = { 'participation': participationTemplate };
                    container.innerHTML = '';
                    container.classList = '';
                    
                    formattedStories.forEach(story => {
                        // console.log('Story:', story);
                        // console.log('Thèmes pour', story.title, ':', story.themes);
                        const rendu = Mustache.render(templateText, story, partials);
                        container.innerHTML += rendu;
                    });
                });
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('stories-container').innerHTML = '<p>Erreur lors du chargement.</p>';
        });
}

function lazyLoadStory(storyId) {
    const container = document.getElementById('story-container');
    container.classList.add('loading');

    // Appel à l'API pour récupérer l'histoire
    fetch(`/serve/stories/${storyId}`)
        .then(response => response.json())
        .then(story => {
            Promise.all([
                fetch(`/serve/users/${story.user_id}`).then(res => res.json()),
                fetch(`/serve/participations/${story.id}`).then(res => res.json())
                    .then(participations => Promise.all(
                        participations.map(p => 
                            fetch(`/serve/users/${p.user_id}`)
                                .then(res => res.json())
                                .then(userData => ({
                                    content: p.content,
                                    author: userData.userName || 'Inconnu',
                                    avatar: userData.avatar,
                                    creationDate: p.creationDate
                                }))
                        )
                    ))
            ])
            .then(([userData, formattedParticipations]) => {
                const formattedStory = {
                    id: story.id,
                    title: story.title,
                    author: userData.username || 'Inconnu',
                    authorAvatar: userData.avatar,
                    participationNumber: formattedParticipations.length,
                    likes: story.likes,
                    participations: formattedParticipations,
                    themes: story.themes || [],
                    creationDate: story.creation_date
                };

                Promise.all([
                    fetch('/api/templates/storycard.mustache').then(response => response.text()),
                    fetch('/api/templates/partials/participation.mustache').then(response => response.text())
                ])
                .then(([templateText, participationTemplate]) => {
                    Mustache.parse(participationTemplate);
                    const partials = { 'participation': participationTemplate };
                    container.innerHTML = '';
                    container.classList.remove('loading');
                    
                    const rendu = Mustache.render(templateText, formattedStory, partials);
                    container.innerHTML = rendu;
                });
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
            container.innerHTML = '<p class="text-red-500">Erreur lors du chargement de l\'histoire.</p>';
        });
}
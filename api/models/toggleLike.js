function toggleLike(storyId) {
    fetch('/api/controllers/checkLogin.php')
        .then(response => response.json())
        .then(data => {
            if (!data.isLoggedIn) {
                window.location.href = '/auth'; // Redirection vers la page de connexion
                return;
            }
            
            // Détermine si nous devons ajouter ou retirer un like
            const likeButton = document.getElementById('like-btn-'+storyId);
            const likesCount = document.getElementById('likes-count-'+storyId);
            const isLiked = likeButton.classList.contains('liked');
            const action = isLiked ? 'unlike' : 'like';
            
            // Changement visuel immédiat (optimiste)
            const currentLikes = parseInt(likesCount.textContent);
            const newLikes = isLiked ? currentLikes - 1 : currentLikes + 1;
            
            if (!isLiked) {
                likeButton.classList.add('liked');
                likeButton.querySelector('img').src = '/api/public/svg/heart-fill.svg';
                likeButton.querySelector('img').classList.add('transform', 'transition-transform', 'duration-300');
                likeButton.querySelector('img').style.transform = 'scale(1.3)';
                setTimeout(() => {
                    likeButton.querySelector('img').style.transform = 'scale(1)';
                }, 300);
            } else {
                likeButton.classList.remove('liked');
                likeButton.querySelector('img').src = '/api/public/svg/heart.svg';
                likeButton.querySelector('img').classList.add('transform', 'transition-transform', 'duration-300');
                likeButton.querySelector('img').style.transform = 'scale(0.8)';
                setTimeout(() => {
                    likeButton.querySelector('img').style.transform = 'scale(1)';
                }, 300);
            }
            
            likesCount.textContent = newLikes;
            
            fetch(`/serve/stories/${storyId}?action=${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include'
            })
            .then(response => response.json())
            .then(result => {
                if (!result.success) {
                    // En cas d'échec, revenir à l'état précédent
                    if (!isLiked) {
                        likeButton.classList.remove('liked');
                        likeButton.querySelector('img').src = '/api/public/svg/heart.svg';
                    } else {
                        likeButton.classList.add('liked');
                        likeButton.querySelector('img').src = '/api/public/svg/heart-fill.svg';
                    }
                    likesCount.textContent = currentLikes;
                }
            })
            .catch(error => {
                // En cas d'erreur, revenir à l'état précédent
                if (!isLiked) {
                    likeButton.classList.remove('liked');
                    likeButton.querySelector('img').src = '/api/public/svg/heart.svg';
                } else {
                    likeButton.classList.add('liked');
                    likeButton.querySelector('img').src = '/api/public/svg/heart-fill.svg';
                }
                likesCount.textContent = currentLikes;
            });
        });
}
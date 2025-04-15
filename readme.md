npx @tailwindcss/cli -i ./api/public/input.css -o ./api/public/global.css --watch


# Lazy loading
Nous avond rencontré un problème de chargement lent sur l'acceuil alors nous avons mis en place un lazy loading sur les histoires et les commentaires.

# Script SQL
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS story_db;
USE story_db;

-- Table Users
CREATE TABLE Users (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    userName VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    bio TEXT,
    avatar VARCHAR(255),
    creationDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    isActive BOOLEAN DEFAULT TRUE,
    isAdmin BOOLEAN DEFAULT FALSE
);

-- Table Stories
CREATE TABLE Stories (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    likes INT DEFAULT 0,
    creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table Themes
CREATE TABLE Themes (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL
);

-- Table StoriesThemes (junction)
CREATE TABLE StoriesThemes (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    theme_id VARCHAR(36),
    story_id VARCHAR(36),
    FOREIGN KEY (theme_id) REFERENCES Themes(id) ON DELETE CASCADE,
    FOREIGN KEY (story_id) REFERENCES Stories(id) ON DELETE CASCADE
);

-- Table Participations
CREATE TABLE Participations (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    user_id VARCHAR(36),
    story_id VARCHAR(36),
    content TEXT NOT NULL,
    creationDate DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (story_id) REFERENCES Stories(id) ON DELETE CASCADE
);

-- Table ParticipationImage
CREATE TABLE ParticipationImage (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    participation_id VARCHAR(36),
    user_id VARCHAR(36),
    image_url VARCHAR(255) NOT NULL,
    creationDate DATE NOT NULL,
    FOREIGN KEY (participation_id) REFERENCES Participations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- Table UserLikes (junction)
CREATE TABLE UserLikes (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    story_id VARCHAR(36),
    user_id VARCHAR(36),
    FOREIGN KEY (story_id) REFERENCES Stories(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- Table UserStoriesFollow (junction)
CREATE TABLE UserStoriesFollow (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    user_id VARCHAR(36),
    story_id VARCHAR(36),
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (story_id) REFERENCES Stories(id) ON DELETE CASCADE
);

-- Table ContentType (simule un enum)
CREATE TABLE ContentType (
    id TINYINT PRIMARY KEY,
    type ENUM('text', 'image') NOT NULL
);

-- Insertion des données fictives

-- ContentType
INSERT INTO ContentType (id, type) VALUES
(1, 'text'),
(2, 'image');

-- Users
-- Les IDs sont générés automatiquement par UUID(), mais pour les références, nous insérons avec des valeurs explicites pour la clarté
INSERT INTO Users (id, userName, email, pass, bio, avatar, creationDate, isActive, isAdmin) VALUES
('user1-uuid', 'Jean Dubois', 'jean.dubois@exemple.com', 'motdepasse123', 'Passionné d’histoires fantastiques', 'https://exemple.com/avatars/jean.jpg', '2024-01-10 10:00:00', TRUE, FALSE),
('user2-uuid', 'Julie Martin', 'julie.martin@exemple.com', 'motdepasse456', 'Amatrice de science-fiction', 'https://exemple.com/avatars/julie.jpg', '2024-02-15 12:30:00', TRUE, FALSE),
('user3-uuid', 'Pierre Admin', 'pierre.admin@exemple.com', 'motdepasse789', 'Administrateur et écrivain de mystères', NULL, '2024-03-01 09:15:00', TRUE, TRUE),
('user4-uuid', 'Alice Roy', 'alice.roy@exemple.com', 'motdepasse101', NULL, 'https://exemple.com/avatars/alice.jpg', '2024-04-20 14:20:00', TRUE, FALSE),
('user5-uuid', 'Charles Leclerc', 'charles.lec@exemple.com', 'motdepasse202', 'Conteur d’aventures', NULL, '2024-05-05 16:45:00', FALSE, FALSE);

-- Themes
INSERT INTO Themes (id, name) VALUES
('theme1-uuid', 'Fantastique'),
('theme2-uuid', 'Science-Fiction'),
('theme3-uuid', 'Mystère'),
('theme4-uuid', 'Aventure'),
('theme5-uuid', 'Romance');

-- Stories
INSERT INTO Stories (id, title, author, likes, creation_date) VALUES
('story1-uuid', 'La Quête du Dragon', 'Jean Dubois', 15, '2024-01-15 08:00:00'),
('story2-uuid', 'Chroniques du Vaisseau', 'Julie Martin', 20, '2024-02-20 10:30:00'),
('story3-uuid', 'L’Indice Caché', 'Pierre Admin', 8, '2024-03-10 11:00:00'),
('story4-uuid', 'Expédition dans la Jungle', 'Alice Roy', 12, '2024-04-25 13:15:00'),
('story5-uuid', 'Amour à Travers le Temps', 'Julie Martin', 25, '2024-05-10 15:00:00');

-- StoriesThemes
INSERT INTO StoriesThemes (id, theme_id, story_id) VALUES
('st1-uuid', 'theme1-uuid', 'story1-uuid'),
('st2-uuid', 'theme2-uuid', 'story2-uuid'),
('st3-uuid', 'theme3-uuid', 'story3-uuid'),
('st4-uuid', 'theme4-uuid', 'story4-uuid'),
('st5-uuid', 'theme5-uuid', 'story5-uuid'),
('st6-uuid', 'theme1-uuid', 'story2-uuid'),
('st7-uuid', 'theme4-uuid', 'story1-uuid');

-- Participations (contenu entre 500 et 1000 caractères, en français)
INSERT INTO Participations (id, user_id, story_id, content, creationDate) VALUES
('part1-uuid', 'user1-uuid', 'story1-uuid', 
    'Le chevalier, Sir Alaric, se tenait ferme face au dragon colossal, ses écailles scintillant comme de l’or fondu sous un ciel écarlate. Son cœur battait fort, non de peur, mais du poids de son serment de protéger le village. Le souffle ardent du monstre calcinait la terre, pourtant Alaric avançait, bouclier levé, épée ornée de runes anciennes. Il se remémorait le récit de l’ancien : seul le courage et la sagesse pouvaient transpercer le cœur du dragon. Lorsque la bête rugit, ébranlant les falaises, Alaric aperçut une faille dans son armure—un espoir fragile. Avec une prière aux vieux dieux, il chargea, porté par son devoir et son destin. Le choc résonna dans la vallée, témoignage de son esprit indomptable. Son épée trouverait-elle sa cible, ou la fureur du dragon l’engloutirait-elle ? Le village retenait son souffle, attendant l’issue de ce duel fatidique.', 
    '2024-01-16'),
('part2-uuid', 'user2-uuid', 'story2-uuid', 
    'La capitaine Élara pilotait le vaisseau *Odyssée* à travers le vortex tourbillonnant du trou de ver, son équipage prêt à affronter l’inconnu. La coque grinçait sous la tension de l’espace-temps déformé, les lumières clignotant tandis que l’IA de navigation recalculait. Élara réfléchissait à toute vitesse : au-delà de cette anomalie s’étendaient des galaxies inexplorées, peut-être pleines de vie ou de dangers. Elle jeta un regard à son second, Kael, dont le signe de tête raffermit sa détermination. La mission était claire : cartographier ce secteur et trouver des ressources pour les colonies mourantes de la Terre. Alors que les couleurs du trou de ver dansaient sur l’écran, Élara ressentit une vague d’émerveillement. Quelles civilisations les attendaient ? Quels secrets se cachaient dans le vide cosmique ? L’*Odyssée* émergea dans un champ stellaire illuminé par deux soleils, et l’équipage retint son souffle.', 
    '2024-02-21'),
('part3-uuid', 'user3-uuid', 'story3-uuid', 
    'Dans la bibliothèque poussiéreuse du manoir Ravenwood, la détective Clara Thorne découvrit un indice qui la glaça. Derrière un panneau secret se trouvait un journal aux pages jaunies, l’encre presque effacée, décrivant une société secrète qui dominait autrefois la ville. Les notes cryptiques parlaient de rituels, de trahisons et d’un artefact perdu—la Clé d’Obsidienne—capable d’ouvrir un coffre de savoir interdit. La torche de Clara vacillait tandis qu’elle lisait, le silence troublé par le craquement des vieux planchers. Chaque mot approfondissait le mystère : qui avait caché ce journal, et pourquoi ? Son partenaire, l’inspecteur Reed, prônait la prudence, mais la curiosité de Clara l’emportait sur sa peur. Les indices pointaient vers l’ancienne chapelle, un lieu évité des habitants. En refermant le journal, une ombre bougea dans un coin—était-elle seule ? La quête de la vérité ne faisait que commencer.', 
    '2024-03-11'),
('part4-uuid', 'user4-uuid', 'story4-uuid', 
    'L’exploratrice Maya Vance se tenait au bord de la rivière tumultueuse, ses eaux bouillonnant de l’esprit sauvage de la jungle. Son équipe, épuisée après des jours à tailler à travers les lianes épaisses, comptait sur elle pour trouver un passage. La carte ancienne, serrée dans ses mains calleuses, désignait cette rivière comme le seuil de la cité perdue de Xal’thar. Maya scrutait la rive opposée, où la brume s’accrochait aux arbres noueux, dissimulant des dangers inconnus. Elle installa un pont de cordes, ses gestes précis malgré l’humidité qui sapait ses forces. Alors que son équipe traversait, le grondement d’un jaguar résonna, et Maya posa la main sur son machette. La jungle testait sa détermination, mais la promesse des ruines dorées de Xal’thar la poussait en avant. Chaque pas était un risque—serpents, pièges ou chasseurs de trésors rivaux pouvaient frapper. Pourtant, le cœur de Maya vibrait d’exaltation.', 
    '2024-04-26'),
('part5-uuid', 'user2-uuid', 'story5-uuid', 
    'L’amour d’Élara et Kael était une flamme traversant les siècles, défiant les rouages froids du temps. Au XXIIIe siècle, sur une Terre ravagée par la guerre, ils s’étaient promis de se retrouver, leurs mains enlacées. Le dispositif temporel, une relique interdite, bourdonna quand Élara entra dans sa lumière, ses yeux fixés sur Kael. Elle se réveilla en France médiévale, guérisseuse dans un village frappé par la peste, le cœur brisé sans lui. Des années passèrent, mais Kael hantait ses rêves. Dans un marché animé, elle le vit—un chevalier, ses yeux inchangés. Leur réunion fut brève, arrachée par une faille temporelle. Elle sauta encore, atterrissant dans une cité futuriste illuminée de néons. Chaque époque testait leur lien, mais l’amour d’Élara était sa boussole. Dans chaque vie, elle cherchait Kael, leurs âmes unies par un serment plus fort que le temps. Cette ère les réunirait-elle enfin ?', 
    '2024-05-11'),
('part6-uuid', 'user1-uuid', 'story2-uuid', 
    'Les capteurs de l’*Odyssée* signalèrent une planète surgissant du néant, sa surface scintillant de flèches cristallines. La capitaine Élara ordonna une analyse, son équipage vibrant d’excitation. Les données étaient inédites—des signatures énergétiques élevées, peut-être artificielles. Était-ce l’œuvre d’une civilisation avancée ? Élara enfila sa combinaison, menant elle-même l’équipe au sol. L’air de la planète était respirable, sa gravité légère, mais un bourdonnement étrange pulsait dans le sol. En approchant une structure imposante, des glyphes s’illuminèrent sur sa surface, mouvants comme des étoiles liquides. La linguiste d’Élara, fascinée, tenta de déchiffrer les symboles, mais leur sens restait obscur. Était-ce un message, une mise en garde ? Alors que le vent portait un murmure étrange, Élara sentit que cette découverte changerait leur destin. La planète cachait un secret, et elle était déterminée à le percer.', 
    '2024-02-22');

-- ParticipationImage
INSERT INTO ParticipationImage (id, participation_id, user_id, image_url, creationDate) VALUES
('img1-uuid', 'part1-uuid', 'user1-uuid', 'https://exemple.com/images/dragon.jpg', '2024-01-16'),
('img2-uuid', 'part2-uuid', 'user2-uuid', 'https://exemple.com/images/trou_de_ver.jpg', '2024-02-21'),
('img3-uuid', 'part4-uuid', 'user4-uuid', 'https://exemple.com/images/riviere.jpg', '2024-04-26');

-- UserLikes
INSERT INTO UserLikes (id, story_id, user_id) VALUES
('like1-uuid', 'story1-uuid', 'user2-uuid'),
('like2-uuid', 'story1-uuid', 'user4-uuid'),
('like3-uuid', 'story2-uuid', 'user1-uuid'),
('like4-uuid', 'story3-uuid', 'user4-uuid'),
('like5-uuid', 'story5-uuid', 'user3-uuid'),
('like6-uuid', 'story4-uuid', 'user2-uuid');

-- UserStoriesFollow
INSERT INTO UserStoriesFollow (id, user_id, story_id) VALUES
('follow1-uuid', 'user1-uuid', 'story2-uuid'),
('follow2-uuid', 'user2-uuid', 'story1-uuid'),
('follow3-uuid', 'user3-uuid', 'story4-uuid'),
('follow4-uuid', 'user4-uuid', 'story5-uuid'),
('follow5-uuid', 'user2-uuid', 'story3-uuid');
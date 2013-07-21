SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(255) NOT NULL,
  UNIQUE KEY `poll_id_2` (`id`,`text`),
  KEY `poll_id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=105 ;

INSERT INTO `answer` (`id`, `text`) VALUES
(87, 'Обязательно'),
(88, 'верю в боге'),
(89, 'Да'),
(90, 'Нет'),
(91, 'Раз в месяц'),
(92, 'Раз в неделю'),
(93, '18-20'),
(94, '21-23');

CREATE TABLE IF NOT EXISTS `answer_user` (
  `answer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `answer_id` (`answer_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `answer_user` (`answer_id`, `user_id`) VALUES
(89, 76),
(91, 76),
(94, 76),
(89, 77),
(91, 77),
(94, 77),
(90, 78),
(92, 78),
(93, 78);

CREATE TABLE IF NOT EXISTS `poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) DEFAULT NULL,
  `text` varchar(255) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=106 ;

INSERT INTO `poll` (`id`, `status`, `text`) VALUES
(97, 0, 'Второй опрос'),
(98, 1, 'Великий опрос');

CREATE TABLE IF NOT EXISTS `poll_question` (
  `poll_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  UNIQUE KEY `poll_id` (`poll_id`,`question_id`),
  KEY `poll_question_ibfk_2` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `poll_question` (`poll_id`, `question_id`) VALUES
(97, 81),
(98, 82),
(98, 83),
(98, 84);

CREATE TABLE IF NOT EXISTS `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_multiple` tinyint(1) NOT NULL,
  `is_required` tinyint(1) NOT NULL,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=91 ;

INSERT INTO `question` (`id`, `is_multiple`, `is_required`, `text`) VALUES
(81, 0, 1, 'Верите в магию?'),
(82, 0, 1, 'Вам оно надо?'),
(83, 0, 0, 'Часто проходите опросы?'),
(84, 0, 0, 'Сколько вам лет?');

CREATE TABLE IF NOT EXISTS `question_answer` (
  `answer_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  UNIQUE KEY `answer_id` (`answer_id`,`question_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `question_answer` (`answer_id`, `question_id`) VALUES
(87, 81),
(88, 81),
(89, 82),
(90, 82),
(91, 83),
(92, 83),
(93, 84),
(94, 84);

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=79 ;

INSERT INTO `user` (`id`, `create_time`) VALUES
(66, '2013-07-21 08:58:41'),
(67, '2013-07-21 09:03:40'),
(70, '2013-07-21 09:07:25'),
(74, '2013-07-21 09:19:38'),
(75, '2013-07-21 10:14:53'),
(76, '2013-07-21 10:23:15'),
(77, '2013-07-21 10:23:33'),
(78, '2013-07-21 10:23:44');


ALTER TABLE `answer_user`
  ADD CONSTRAINT `answer_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `answer_user_ibfk_1` FOREIGN KEY (`answer_id`) REFERENCES `answer` (`id`) ON DELETE CASCADE;

ALTER TABLE `poll_question`
  ADD CONSTRAINT `poll_question_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poll_question_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `poll` (`id`) ON DELETE CASCADE;

ALTER TABLE `question_answer`
  ADD CONSTRAINT `question_answer_ibfk_1` FOREIGN KEY (`answer_id`) REFERENCES `answer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `question_answer_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE;

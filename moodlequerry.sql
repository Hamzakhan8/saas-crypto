   
-- New Query
------------------------------------------------------------------------------------
SELECT 
CONCAT('https://online-learning.aau.ac.ae/moodle/mod/quiz/view.php?id=', 
	(
    SELECT cm.id AS course_module_id
	FROM mdl_course_modules cm
	WHERE cm.instance = q.id AND cm.module = (SELECT id FROM mdl_modules WHERE name = 'quiz')
    )
) AS quiz_link,
    name AS quiz_title,
    course AS course_id,
    FROM_UNIXTIME(q.timeopen) AS quiz_time_start,
    FROM_UNIXTIME(q.timeclose) AS quiz_time_end,
    (SELECT 
            fullname
        FROM
            mdl_course
        WHERE
            id = course_id) AS course_name,
    
    (SELECT DISTINCT
            CONCAT(u.firstname, ' ', u.lastname)
        FROM
            mdl_role_assignments AS ra	
                JOIN
            mdl_user AS u ON ra.userid = u.id
                JOIN
            mdl_context AS ctx ON ctx.id = ra.contextid
        WHERE
            ra.roleid = 3 AND ctx.instanceid = c.id
                AND ctx.contextlevel = 50
        LIMIT 1) AS Teacher,
    (SELECT 
            CASE
                    WHEN x.requiresafeexambrowser = 4 THEN 'Used'
                    ELSE 'Not Used'
                END
        FROM
            mdl_quizaccess_seb_quizsettings AS x
        WHERE
            x.quizid = q.id) AS browsersecurity,
    (SELECT DISTINCT
            CASE
                    WHEN x.quizid = 0 THEN 'Not Used'
                    ELSE 'Used'
                END
        FROM
            mdl_quizaccess_ipaddresslist AS x
        WHERE
            x.quizid = q.id) AS restriction,
	 (SELECT 
            COUNT(DISTINCT (userid))        
		FROM mdl_quiz_attempts as m
		JOIN mdl_user AS u ON m.userid = u.id
        WHERE
            m.quiz = q.id AND u.username LIKE '%20%') AS attempts,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs,
            mdl_quiz_slots s
        WHERE
            s.quizid = q.id AND qs.id = s.questionid
                AND (SELECT DISTINCT
                    qtype
                FROM
                    mdl_question
                WHERE
                    category = qs.category AND parent = 0
                LIMIT 1) = 'multichoice') AS randommultichoice,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
                LEFT JOIN
            mdl_quiz_slots s ON qs.id = s.questionid
        WHERE
            qs.qtype = 'multichoice'
                AND s.quizid = q.id) AS multichoice,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
        WHERE
            qs.category IN (SELECT DISTINCT
                    s.questioncategoryid
                FROM
                    mdl_quiz_slots s
                WHERE
                    s.quizid = q.id)
                AND qs.qtype = 'multichoice') AS total_bank_mcq,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs,
            mdl_quiz_slots s
        WHERE
            s.quizid = q.id AND qs.id = s.questionid
                AND (SELECT DISTINCT
                    qtype
                FROM
                    mdl_question
                WHERE
                    category = qs.category AND parent = 0
                LIMIT 1) = 'essay') AS randonessay,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
                LEFT JOIN
            mdl_quiz_slots s ON qs.id = s.questionid
        WHERE
            qs.qtype = 'essay' AND s.quizid = q.id) AS essay,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
        WHERE
            qs.category IN (SELECT DISTINCT
                    s.questioncategoryid
                FROM
                    mdl_quiz_slots s
                WHERE
                    s.quizid = q.id)
                AND qs.qtype = 'essay') AS total_essay,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
                LEFT JOIN
            mdl_quiz_slots s ON qs.id = s.questionid
        WHERE
            qs.qtype = 'random' AND s.quizid = q.id) AS random,
    (SELECT 
            COUNT(*)
        FROM
            mdl_quiz_slots AS x
        WHERE
            q.id = x.quizid) AS num_questions,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
        WHERE
            qs.category IN (SELECT DISTINCT
                    s.questioncategoryid
                FROM
                    mdl_quiz_slots s
                WHERE
                    s.quizid = q.id)) AS total_bank,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
                LEFT JOIN
            mdl_quiz_slots s ON qs.id = s.questionid
        WHERE
            qs.qtype = 'multianswer'
                AND s.quizid = q.id) AS Embeddedanswer,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
                LEFT JOIN
            mdl_quiz_slots s ON qs.id = s.questionid
        WHERE
            qs.qtype = 'match' AND s.quizid = q.id) AS mmatch,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs,
            mdl_quiz_slots s
        WHERE
            s.quizid = q.id AND qs.id = s.questionid
                AND (SELECT DISTINCT
                    qtype
                FROM
                    mdl_question
                WHERE
                    category = qs.category AND parent = 0
                LIMIT 1) = 'match') AS randonmatch,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
                LEFT JOIN
            mdl_quiz_slots s ON qs.id = s.questionid
        WHERE
            qs.qtype = 'truefalse'
                AND s.quizid = q.id) AS truefalse,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs,
            mdl_quiz_slots s
        WHERE
            s.quizid = q.id AND qs.id = s.questionid
                AND (SELECT DISTINCT
                    qtype
                FROM
                    mdl_question
                WHERE
                    category = qs.category AND parent = 0
                LIMIT 1) = 'truefalse') AS randomtruefalse,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
                LEFT JOIN
            mdl_quiz_slots s ON qs.id = s.questionid
        WHERE
            qs.qtype = 'shortanswer'
                AND s.quizid = q.id) AS shortanswer,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs,
            mdl_quiz_slots s
        WHERE
            s.quizid = q.id AND qs.id = s.questionid
                AND (SELECT DISTINCT
                    qtype
                FROM
                    mdl_question
                WHERE
                    category = qs.category AND parent = 0
                LIMIT 1) = 'shortanswer') AS randomshortanswer,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
                LEFT JOIN
            mdl_quiz_slots s ON qs.id = s.questionid
        WHERE
            qs.qtype = 'numerical'
                AND s.quizid = q.id) AS numerical,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
                LEFT JOIN
            mdl_quiz_slots s ON qs.id = s.questionid
        WHERE
            qs.qtype = 'gapfill' AND s.quizid = q.id) AS gapfill,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
                LEFT JOIN
            mdl_quiz_slots s ON qs.id = s.questionid
        WHERE
            qs.qtype = 'description'
                AND s.quizid = q.id) AS description,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs,
            mdl_quiz_slots s
        WHERE
            s.quizid = q.id AND qs.id = s.questionid
                AND (SELECT DISTINCT
                    qtype
                FROM
                    mdl_question
                WHERE
                    category = qs.category AND parent = 0
                LIMIT 1) = 'description') AS randondescription,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
                LEFT JOIN
            mdl_quiz_slots s ON qs.id = s.questionid
        WHERE
            qs.qtype = 'ddwtos' AND s.quizid = q.id) AS ddwtos,
    (SELECT 
            COUNT(*)
        FROM
            mdl_question qs
                LEFT JOIN
            mdl_quiz_slots s ON qs.id = s.questionid
        WHERE
            qs.qtype = 'ddimageortext'
                AND s.quizid = q.id) AS dragdrop
FROM
    mdl_quiz q
        JOIN
    mdl_course c ON q.course = c.id
WHERE
    FROM_UNIXTIME(q.timeopen) BETWEEN '2023-12-07 16:20:00' AND '2023-12-07 18:30:00'
        AND (SELECT 
            COUNT(DISTINCT (userid))
        FROM mdl_quiz_attempts as m
		JOIN mdl_user AS u ON m.userid = u.id
        WHERE
            m.quiz = q.id) > 0
            order by q.timeopen;
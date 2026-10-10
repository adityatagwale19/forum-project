-- Demo data for local development only.
--   mysql -u root -p forum_website < database/seed.sql
-- Every demo account has the password demo1234. Never load this file on a public server.

SET NAMES utf8mb4;

INSERT INTO users (id, username, email, password_hash) VALUES
(1, 'aditya',  'aditya@example.com',  '$2y$12$nAxFgHg8uJNI1nM6VKR0Le97sJgQLTk0jmmuIfxh6q9539x1/gFZ.'),
(2, 'meera',   'meera@example.com',   '$2y$12$C8XYciNVDJ.qZjPcNC2YQuBZqU2WtFR4884mkcP2ceXkb2SRfnC9i'),
(3, 'rohit',   'rohit@example.com',   '$2y$12$1VwzsgyU5XULkBPrb4ozK.iNbW1jqKRvTyoFSxNoL4gv.IVDA0736'),
(4, 'sandeep', 'sandeep@example.com', '$2y$12$x42rXmfDK8MRw0OYU8Atee2uKM/qDNoNDDCUY7ka0uOuaePIIEeF6');

INSERT INTO categories (id, name, description, image) VALUES
(1, 'PHP', 'Plain PHP, PSR standards, composer and everything around the language itself.', 'banner-1.jpg'),
(2, 'MySQL', 'Schema design, indexes, slow queries and the things that bite once a table gets big.', 'banner-2.jpg'),
(3, 'WordPress', 'Plugin and theme work, hooks, the REST API and custom admin screens.', 'banner-3.jpg'),
(4, 'JavaScript', 'Browser APIs, fetch, the DOM and small front end problems.', 'banner-1.jpg'),
(5, 'Security', 'Input validation, prepared statements, sessions, CSRF and password storage.', 'banner-2.jpg'),
(6, 'Career', 'Learning paths, portfolios, interviews and working as a developer in India.', 'banner-3.jpg');

INSERT INTO threads (id, category_id, user_id, title, body) VALUES
(1, 1, 1, 'When is a static method the wrong choice?',
 'I keep reaching for static methods because they are easy to call from anywhere. Then the tests get hard to write because I cannot swap the dependency. How do you decide?'),
(2, 1, 2, 'Is an autoloader worth writing by hand?',
 'Composer gives me an autoloader for free, but I wanted to understand what it actually does. Writing one with spl_autoload_register took about fifteen lines. Did anyone else try this?'),
(3, 2, 3, 'My query got slow after 50k rows',
 'A simple select with a where on a varchar column was instant on my laptop and takes two seconds in staging. EXPLAIN says it is a full table scan. Adding an index fixed it, but I want to understand why it was fine before.'),
(4, 2, 1, 'utf8 or utf8mb4, does it still matter?',
 'I had a bug where an emoji in a comment broke the insert. Turned out the column was utf8, which is only three bytes per character in MySQL. Moving to utf8mb4 fixed it.'),
(5, 3, 4, 'Where do you put business logic in a plugin?',
 'My main plugin file is 900 lines of add_action calls and the logic is mixed in with the hooks. Trying to split it into classes without breaking anything. Any pattern that worked for you?'),
(6, 3, 2, 'wpdb prepare with an IN clause',
 'prepare does not take an array, so an IN clause needs the placeholders built first. I ended up with implode on an array_fill of question marks. Feels wrong but it works.'),
(7, 4, 3, 'fetch error handling keeps surprising me',
 'A 404 does not reject the promise. I assumed it would, so my catch block never ran and the user saw nothing. Now I check response.ok myself.'),
(8, 5, 1, 'CSRF token per form or per session?',
 'I store one token in the session and check it on every post. Someone told me each form should get its own token. Is the extra work worth it for a small app?'),
(9, 5, 4, 'Why not md5 for passwords?',
 'An old project of mine used md5. I know it is wrong now, but I could not explain why until I read about how fast a GPU can hash. password_hash is the answer, but the reason matters more.'),
(10, 6, 2, 'What makes a portfolio repo worth reading?',
 'I have twenty small repos from when I was learning. Most have no README and no structure. Thinking about upgrading a few properly instead of adding more.');

INSERT INTO comments (id, thread_id, user_id, body) VALUES
(1, 1, 2, 'If the method needs no state and will never need a different implementation, static is fine. The moment you want to fake it in a test, it is the wrong choice.'),
(2, 1, 3, 'I ask myself whether I would ever want two of them. If yes, it becomes an object.'),
(3, 2, 1, 'Worth it once, to see what it does. After that use composer, because it caches the class map.'),
(4, 3, 1, 'Without an index MySQL reads every row. On 500 rows that is nothing. On 50k it shows up.'),
(5, 3, 4, 'Check the cardinality of the column too. An index on a column with two values will not help much.'),
(6, 4, 3, 'utf8mb4 also changes your index size limits, so watch out for long varchar unique keys.'),
(7, 5, 2, 'Keep the hooks in one bootstrap file and let the callbacks be one line that calls a class. Easier to read and easier to test.'),
(8, 5, 1, 'Do it one feature at a time. Moving everything at once is how you break a release.'),
(9, 6, 4, 'That implode approach is the normal way. There is no cleaner option with prepare.'),
(10, 7, 1, 'Same trap here. Only a network failure rejects. Everything the server answers is a resolved promise.'),
(11, 8, 2, 'One token per session is enough for most apps. Per form matters when a page has many forms and you want to stop replay on one of them.'),
(12, 8, 3, 'Rotate the token after login either way, so a token taken before login is useless after.'),
(13, 9, 2, 'md5 is fast, and fast is exactly what you do not want. password_hash is slow on purpose.'),
(14, 9, 3, 'Also no salt by default with md5, so the same password gives the same hash for everyone.'),
(15, 10, 1, 'A README that says what it does, how to run it, and what is missing. That alone puts a repo ahead of most.');

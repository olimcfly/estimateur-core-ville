'use client';

import { useMemo, useState } from 'react';

const PAGE_SIZE = 4;

export default function CommentSection({ initialComments = [] }) {
  const [comments, setComments] = useState(initialComments);
  const [page, setPage] = useState(1);
  const [form, setForm] = useState({ name: '', email: '', message: '' });

  const paginatedComments = useMemo(() => comments.slice(0, page * PAGE_SIZE), [comments, page]);

  const onSubmit = (event) => {
    event.preventDefault();

    if (!form.name || !form.email || !form.message) return;

    const nextComment = {
      id: `comment-${Date.now()}`,
      author: form.name,
      dateLabel: 'À l’instant',
      message: form.message,
      reported: false,
      replies: [],
    };

    setComments((current) => [nextComment, ...current]);
    setForm({ name: '', email: '', message: '' });
  };

  const onReport = (id) => {
    setComments((current) =>
      current.map((comment) => (comment.id === id ? { ...comment, reported: true } : comment))
    );
  };

  return (
    <section id="commentaires" className="space-y-6 rounded-2xl border border-slate-200 bg-white p-6">
      <header className="flex items-center justify-between">
        <h2 className="text-xl font-semibold text-slate-900">Commentaires</h2>
        <p className="text-sm text-slate-500">{comments.length} au total</p>
      </header>

      <form onSubmit={onSubmit} className="grid gap-3 rounded-xl bg-slate-50 p-4 md:grid-cols-2">
        <input
          type="text"
          placeholder="Nom"
          value={form.name}
          onChange={(e) => setForm((current) => ({ ...current, name: e.target.value }))}
          className="rounded-lg border border-slate-300 px-3 py-2 text-sm"
        />
        <input
          type="email"
          placeholder="Email"
          value={form.email}
          onChange={(e) => setForm((current) => ({ ...current, email: e.target.value }))}
          className="rounded-lg border border-slate-300 px-3 py-2 text-sm"
        />
        <textarea
          placeholder="Votre message"
          value={form.message}
          onChange={(e) => setForm((current) => ({ ...current, message: e.target.value }))}
          rows={4}
          className="rounded-lg border border-slate-300 px-3 py-2 text-sm md:col-span-2"
        />
        <button
          type="submit"
          className="w-fit rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white md:col-span-2"
        >
          Publier
        </button>
      </form>

      <ul className="space-y-4">
        {paginatedComments.map((comment) => (
          <li key={comment.id} className="rounded-xl border border-slate-200 p-4">
            <div className="flex items-center justify-between">
              <p className="text-sm font-semibold text-slate-900">{comment.author}</p>
              <span className="text-xs text-slate-500">{comment.dateLabel}</span>
            </div>
            <p className="mt-2 text-sm text-slate-700">{comment.message}</p>

            <button
              type="button"
              disabled={comment.reported}
              onClick={() => onReport(comment.id)}
              className="mt-2 text-xs text-red-600 disabled:text-slate-400"
            >
              {comment.reported ? 'Commentaire signalé' : 'Signaler commentaire'}
            </button>

            {comment.replies?.length ? (
              <ul className="mt-3 space-y-2 border-l border-slate-200 pl-4">
                {comment.replies.slice(0, 1).map((reply) => (
                  <li key={reply.id} className="rounded-lg bg-slate-50 p-3 text-sm">
                    <p className="font-medium text-slate-800">{reply.author}</p>
                    <p className="text-slate-700">{reply.message}</p>
                  </li>
                ))}
              </ul>
            ) : null}
          </li>
        ))}
      </ul>

      {paginatedComments.length < comments.length ? (
        <button
          type="button"
          onClick={() => setPage((current) => current + 1)}
          className="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700"
        >
          Charger plus de commentaires
        </button>
      ) : null}
    </section>
  );
}

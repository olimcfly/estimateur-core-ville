"use client";

import { useMemo, useState } from "react";

const initialConversations = [
  {
    id: 1,
    with: "Agence Lumière",
    lastMessage: "Le bien est disponible pour visite samedi.",
    unread: true,
    messages: [
      { from: "agent", text: "Bonjour, souhaitez-vous organiser une visite ?" },
      { from: "me", text: "Oui, samedi matin si possible." },
      { from: "agent", text: "Le bien est disponible pour visite samedi." },
    ],
  },
  {
    id: 2,
    with: "Claire - Agent immobilier",
    lastMessage: "Je vous joins la fiche complète.",
    unread: false,
    messages: [
      { from: "agent", text: "Je vous joins la fiche complète." },
    ],
  },
];

export default function MessagesPage() {
  const [conversations, setConversations] = useState(initialConversations);
  const [activeId, setActiveId] = useState(initialConversations[0].id);
  const [draft, setDraft] = useState("");

  const activeConversation = useMemo(
    () => conversations.find((c) => c.id === activeId),
    [conversations, activeId]
  );

  const sendMessage = (e) => {
    e.preventDefault();
    if (!draft.trim()) return;
    setConversations((all) =>
      all.map((conv) =>
        conv.id === activeId
          ? {
              ...conv,
              lastMessage: draft,
              unread: false,
              messages: [...conv.messages, { from: "me", text: draft }],
            }
          : conv
      )
    );
    setDraft("");
  };

  return (
    <section className="grid h-[calc(100vh-60px)] grid-cols-1 gap-4 p-4 sm:p-6 lg:h-screen lg:grid-cols-[300px,1fr]">
      <aside className="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <h1 className="text-xl font-bold">Messagerie</h1>
        <ul className="mt-4 space-y-2">
          {conversations.map((conv) => (
            <li key={conv.id}>
              <button
                onClick={() => {
                  setActiveId(conv.id);
                  setConversations((all) =>
                    all.map((c) => (c.id === conv.id ? { ...c, unread: false } : c))
                  );
                }}
                className="w-full rounded-xl border border-slate-200 p-3 text-left hover:bg-slate-50"
              >
                <p className="flex items-center justify-between text-sm font-semibold">
                  {conv.with}
                  {conv.unread && <span className="h-2 w-2 rounded-full bg-rose-500" />}
                </p>
                <p className="mt-1 truncate text-sm text-slate-500">{conv.lastMessage}</p>
              </button>
            </li>
          ))}
        </ul>
      </aside>

      <article className="flex flex-col rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <header className="border-b border-slate-100 pb-3">
          <p className="font-semibold text-slate-900">{activeConversation?.with}</p>
          <p className="text-xs text-slate-500">Pièces jointes autorisées (PDF, JPG)</p>
        </header>

        <div className="flex-1 space-y-2 overflow-y-auto py-4">
          {activeConversation?.messages.map((msg, idx) => (
            <div
              key={idx}
              className={`max-w-[80%] rounded-xl px-3 py-2 text-sm ${
                msg.from === "me"
                  ? "ml-auto bg-blue-600 text-white"
                  : "bg-slate-100 text-slate-700"
              }`}
            >
              {msg.text}
            </div>
          ))}
        </div>

        <form onSubmit={sendMessage} className="mt-2 flex flex-col gap-2 sm:flex-row">
          <input
            value={draft}
            onChange={(e) => setDraft(e.target.value)}
            placeholder="Écrire un message..."
            className="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm"
          />
          <input type="file" className="text-xs" />
          <button className="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white">
            Envoyer
          </button>
        </form>
      </article>
    </section>
  );
}

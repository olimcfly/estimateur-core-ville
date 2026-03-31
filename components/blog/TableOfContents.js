'use client';

import { useEffect, useMemo, useState } from 'react';

function createIdFromText(text = '') {
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)+/g, '');
}

export default function TableOfContents({ containerSelector = '.article-content' }) {
  const [headings, setHeadings] = useState([]);
  const [activeId, setActiveId] = useState('');
  const [isOpen, setIsOpen] = useState(false);

  useEffect(() => {
    const container = document.querySelector(containerSelector);
    if (!container) return;

    const nodes = Array.from(container.querySelectorAll('h2, h3'));
    const parsed = nodes.map((node) => {
      const id = node.id || createIdFromText(node.textContent);
      if (!node.id) node.id = id;
      return { id, label: node.textContent, level: node.tagName.toLowerCase() };
    });

    setHeadings(parsed);

    const observer = new IntersectionObserver(
      (entries) => {
        const visible = entries.filter((entry) => entry.isIntersecting);
        if (visible[0]) setActiveId(visible[0].target.id);
      },
      { rootMargin: '-35% 0px -55% 0px', threshold: 0.1 }
    );

    nodes.forEach((node) => observer.observe(node));
    return () => observer.disconnect();
  }, [containerSelector]);

  const content = useMemo(
    () => (
      <ul className="space-y-2 text-sm">
        {headings.map((heading) => (
          <li key={heading.id} className={heading.level === 'h3' ? 'pl-4' : ''}>
            <a
              href={`#${heading.id}`}
              onClick={(event) => {
                event.preventDefault();
                document.getElementById(heading.id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
              }}
              className={`block rounded-md px-2 py-1 transition ${
                activeId === heading.id ? 'bg-blue-50 font-semibold text-blue-700' : 'text-slate-600 hover:bg-slate-100'
              }`}
            >
              {heading.label}
            </a>
          </li>
        ))}
      </ul>
    ),
    [activeId, headings]
  );

  if (!headings.length) return null;

  return (
    <aside className="rounded-2xl border border-slate-200 bg-white p-4 lg:sticky lg:top-24">
      <button
        type="button"
        onClick={() => setIsOpen((current) => !current)}
        className="flex w-full items-center justify-between text-left text-sm font-semibold text-slate-900 lg:cursor-default"
      >
        Sommaire
        <span className="lg:hidden">{isOpen ? '−' : '+'}</span>
      </button>

      <div className={`mt-3 ${isOpen ? 'block' : 'hidden'} lg:block`}>{content}</div>
    </aside>
  );
}

"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useMemo } from "react";

const navigation = [
  { href: "/dashboard", label: "Accueil", icon: "🏠" },
  { href: "/dashboard/profil", label: "Profil", icon: "👤" },
  { href: "/dashboard/alertes", label: "Alertes", icon: "🔔" },
  { href: "/dashboard/messages", label: "Messages", icon: "💬" },
];

function NavLink({ item, pathname, badge = 0 }) {
  const isActive = pathname === item.href;
  return (
    <Link
      href={item.href}
      className={`group flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium transition ${
        isActive
          ? "bg-blue-600 text-white"
          : "text-slate-600 hover:bg-slate-100 hover:text-slate-900"
      }`}
    >
      <span className="flex items-center gap-2">
        <span aria-hidden>{item.icon}</span>
        {item.label}
      </span>
      {badge > 0 && (
        <span className="rounded-full bg-rose-500 px-2 py-0.5 text-xs text-white">
          {badge}
        </span>
      )}
    </Link>
  );
}

export default function DashboardLayout({ children }) {
  const pathname = usePathname();
  const router = useRouter();

  const user = useMemo(
    () => ({
      name: "Sofia Martin",
      email: "sofia.martin@email.com",
      avatar:
        "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=120&q=80&auto=format&fit=crop",
      unread: 3,
    }),
    []
  );

  const handleLogout = () => {
    document.cookie = "auth-token=; path=/; max-age=0";
    router.push("/login");
  };

  return (
    <div className="min-h-screen bg-slate-50">
      <aside className="fixed inset-y-0 left-0 hidden w-[240px] flex-col border-r border-slate-200 bg-white p-4 lg:flex">
        <div className="mb-8 flex items-center gap-3 px-2">
          <img
            src={user.avatar}
            alt="Avatar utilisateur"
            className="h-10 w-10 rounded-full object-cover"
          />
          <div>
            <p className="text-sm font-semibold text-slate-900">{user.name}</p>
            <p className="text-xs text-slate-500">Espace client</p>
          </div>
        </div>

        <nav className="space-y-2">
          {navigation.map((item) => (
            <NavLink
              key={item.href}
              item={item}
              pathname={pathname}
              badge={item.href.includes("messages") ? user.unread : 0}
            />
          ))}
        </nav>

        <button
          type="button"
          onClick={handleLogout}
          className="mt-auto rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100"
        >
          Déconnexion
        </button>
      </aside>

      <main className="pb-[72px] lg:ml-[240px] lg:pb-0">{children}</main>

      <nav className="fixed inset-x-0 bottom-0 z-20 flex h-[60px] items-center justify-around border-t border-slate-200 bg-white lg:hidden">
        {navigation.map((item) => {
          const isActive = pathname === item.href;
          const showBadge = item.href.includes("messages") && user.unread > 0;
          return (
            <Link
              key={item.href}
              href={item.href}
              className={`relative flex flex-col items-center text-xs ${
                isActive ? "text-blue-600" : "text-slate-500"
              }`}
            >
              <span>{item.icon}</span>
              <span>{item.label}</span>
              {showBadge && (
                <span className="absolute -right-3 top-0 rounded-full bg-rose-500 px-1.5 text-[10px] text-white">
                  {user.unread}
                </span>
              )}
            </Link>
          );
        })}
      </nav>
    </div>
  );
}

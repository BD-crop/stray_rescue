import { useState } from 'react'
import { motion } from 'framer-motion'
import { HeartHandshake, Menu, Sparkles, X } from 'lucide-react'
import { Button } from './Button'

const navItems = [
  { label: 'Home', page: 'home' },
  { label: 'Adopt', page: 'adopt' },
  { label: 'Report', page: 'report' },
  { label: 'Community', page: 'community' },
  { label: 'Shelters', page: 'shelters' },
]

function Navbar({ currentPage, onNavigate }) {
  const [isOpen, setIsOpen] = useState(false)

  const handleNav = (page) => {
    setIsOpen(false)
    onNavigate?.(page)
  }

  return (
    <motion.header
      animate={{ opacity: 1, y: 0 }}
      className="fixed inset-x-0 top-0 z-50 px-0 pt-0"
      initial={{ opacity: 0, y: -18 }}
      transition={{ duration: 0.45, ease: [0.22, 1, 0.36, 1] }}
    >
      <div className="w-full border-b border-orange-100/70 bg-white/88 shadow-[0_16px_40px_rgba(15,23,42,0.07)] backdrop-blur-xl">
        <div className="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-0 ">
          <button
            className="flex items-center gap-4 text-left"
            onClick={() => handleNav('home')}
            type="button"
          >
            <span className="grid h-11 w-11 place-items-center rounded-full bg-orange-100 text-orange-500 shadow-[0_10px_24px_rgba(249,115,22,0.16)]">
              <HeartHandshake className="h-5 w-5" />
            </span>
            <span>
              <span className="block text-lg font-black leading-none tracking-tight text-slate-900">
                Stray<span className="text-orange-500">Rescue</span>
              </span>
              <span className="block text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                Save Lives Together
              </span>
            </span>
          </button>

          <nav className="hidden items-center gap-2 lg:flex" aria-label="Primary">
            {navItems.map((item) => {
              const active =
                currentPage === item.page || (currentPage === 'home' && item.page === 'home')
              return (
                <button
                  className={`rounded-full px-4 py-2 text-sm font-extrabold transition-colors ${active ? 'bg-orange-100 text-orange-600 shadow-[inset_0_0_0_1px_rgba(249,115,22,0.12)]' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-500'}`}
                  key={item.page}
                  onClick={() => handleNav(item.page)}
                  type="button"
                >
                  {item.label}
                </button>
              )
            })}
          </nav>

          <div className="hidden items-center gap-3 lg:flex">
            <div className="hidden items-center gap-2 rounded-full border border-orange-100 bg-orange-50/80 px-3 py-2 xl:inline-flex">
              <Sparkles className="h-4 w-4 text-orange-500" />
              <span className="text-xs font-black uppercase tracking-[0.18em] text-slate-600">
                24/7 Rescue Support
              </span>
            </div>
            <Button onClick={() => handleNav('login')} size="sm" variant="ghost">
              Login
            </Button>
            <Button onClick={() => handleNav('register')} size="sm">
              Get Started
            </Button>
          </div>

          <button
            aria-expanded={isOpen}
            aria-label="Toggle navigation"
            className="grid h-11 w-11 place-items-center rounded-2xl border border-slate-200 text-slate-700 lg:hidden"
            onClick={() => setIsOpen((open) => !open)}
            type="button"
          >
            {isOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
          </button>
        </div>
      </div>

      {isOpen ? (
        <div className="border-t border-orange-100 bg-white/96 p-4 shadow-[0_18px_40px_rgba(15,23,42,0.08)] backdrop-blur lg:hidden">
          <div className="mx-auto grid max-w-7xl gap-2">
            {navItems.map((item) => (
              <button
                className="rounded-2xl px-4 py-3 text-left text-sm font-bold text-slate-700 transition-colors hover:bg-orange-50 hover:text-orange-500"
                key={item.page}
                onClick={() => handleNav(item.page)}
                type="button"
              >
                {item.label}
              </button>
            ))}
            <Button className="mt-2 w-full" onClick={() => handleNav('login')} variant="outline">
              Login
            </Button>
            <Button className="w-full" onClick={() => handleNav('register')}>
              Get Started
            </Button>
          </div>
        </div>
      ) : null}
    </motion.header>
  )
}

export default Navbar

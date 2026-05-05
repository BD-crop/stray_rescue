import { useEffect, useState } from 'react'
import Navbar from './components/Navbar'
import LandingPage from './pages/LandingPage'
import AuthPage from './pages/AuthPage'
import './App.css'

const PAGE_MAP = new Set(['home', 'login', 'register'])

function App() {
  const [currentPage, setCurrentPage] = useState('home')

  const handleNavigate = (page) => {
    if (PAGE_MAP.has(page)) {
      setCurrentPage(page)
      window.scrollTo({ top: 0, behavior: 'smooth' })
      return
    }

    setCurrentPage('home')

    requestAnimationFrame(() => {
      const section = document.getElementById(page)
      if (section) {
        section.scrollIntoView({ behavior: 'smooth', block: 'start' })
      } else {
        window.scrollTo({ top: 0, behavior: 'smooth' })
      }
    })
  }

  useEffect(() => {
    document.title =
      currentPage === 'home'
        ? 'StrayRescue | Save Lives Together'
        : currentPage === 'login'
          ? 'Sign In | StrayRescue'
          : 'Join StrayRescue'
  }, [currentPage])

  return (
    <div className="app-shell">
      <Navbar currentPage={currentPage} onNavigate={handleNavigate} />

      <main>
        {currentPage === 'home' ? (
          <LandingPage onNavigate={handleNavigate} />
        ) : (
          <AuthPage mode={currentPage} onNavigate={handleNavigate} />
        )}
      </main>
    </div>
  )
}

export default App

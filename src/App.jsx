import { useEffect, useState } from 'react'
import Navbar from './components/Navbar'
import LandingPage from './pages/LandingPage'
import AuthPage from './pages/AuthPage'
import AdoptionPage from './pages/AdoptionPage'
import ReportAnimalPage from './pages/ReportAnimalPage'
import CommunityPage from './pages/CommunityPage'
import InteractiveMap from './pages/InteractiveMap'
import ShopPage from './pages/ShopPage'
import UserDashboard from './pages/UserDashboard'
import VolunteerDashboard from './pages/VolunteerDashboard'
import ShelterDashboard from './pages/ShelterDashboard'
import AdminDashboard from './pages/AdminDashboard'
import './App.css'

const PAGE_TITLES = {
  home: 'StrayRescue | Save Lives Together',
  login: 'Sign In | StrayRescue',
  register: 'Join StrayRescue',
  adopt: 'Adopt a Pet | StrayRescue',
  report: 'Report Animal | StrayRescue',
  community: 'Community | StrayRescue',
  map: 'Rescue Map | StrayRescue',
  shop: 'Rescue Shop | StrayRescue',
  'user-dashboard': 'My Dashboard | StrayRescue',
  'volunteer-dashboard': 'Volunteer Dashboard | StrayRescue',
  'shelter-dashboard': 'Shelter Dashboard | StrayRescue',
  'admin-dashboard': 'Admin Dashboard | StrayRescue',
}

const PAGE_MAP = new Set(Object.keys(PAGE_TITLES))

function App() {
  const [currentPage, setCurrentPage] = useState('home')

  const handleNavigate = (page) => {
    if (page === 'volunteer') {
      setCurrentPage('register')
      window.scrollTo({ top: 0, behavior: 'smooth' })
      return
    }

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
    document.title = PAGE_TITLES[currentPage] ?? PAGE_TITLES.home
  }, [currentPage])

  const renderPage = () => {
    switch (currentPage) {
      case 'login':
      case 'register':
        return <AuthPage mode={currentPage} onNavigate={handleNavigate} />
      case 'adopt':
        return <AdoptionPage onNavigate={handleNavigate} />
      case 'report':
        return <ReportAnimalPage onNavigate={handleNavigate} />
      case 'community':
        return <CommunityPage onNavigate={handleNavigate} />
      case 'map':
        return <InteractiveMap onNavigate={handleNavigate} />
      case 'shop':
        return <ShopPage onNavigate={handleNavigate} />
      case 'user-dashboard':
        return <UserDashboard onNavigate={handleNavigate} />
      case 'volunteer-dashboard':
        return <VolunteerDashboard onNavigate={handleNavigate} />
      case 'shelter-dashboard':
        return <ShelterDashboard onNavigate={handleNavigate} />
      case 'admin-dashboard':
        return <AdminDashboard onNavigate={handleNavigate} />
      default:
        return <LandingPage onNavigate={handleNavigate} />
    }
  }

  return (
    <div className="app-shell">
      <Navbar currentPage={currentPage} onNavigate={handleNavigate} />

      <main>{renderPage()}</main>
    </div>
  )
}

export default App

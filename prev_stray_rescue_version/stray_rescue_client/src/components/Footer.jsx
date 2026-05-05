import {
  Earth,
  Heart,
  HeartHandshake,
  Mail,
  MapPin,
  MessageCircleHeart,
  Phone,
  SendHorizontal,
} from 'lucide-react'
import { MotionReveal } from './MotionReveal'

function Footer() {
  const quickLinks = ['About Us', 'How It Works', 'Success Stories', 'Blog']
  const getInvolved = ['Adopt a Pet', 'Become a Volunteer', 'Foster a Pet', 'Donate']

  return (
    <footer className="mt-16 overflow-hidden bg-slate-950 text-slate-300">
      <div className="mx-auto max-w-7xl px-4 pb-8 pt-6 sm:px-6 lg:px-8">
        <MotionReveal
          className="mb-12 grid gap-6 rounded-[30px] border border-white/8 bg-[linear-gradient(135deg,rgba(249,115,22,0.16),rgba(15,23,42,0.3))] p-6 shadow-[0_24px_60px_rgba(0,0,0,0.18)] md:grid-cols-[1.2fr_0.8fr] md:p-8"
          y={24}
        >
          <div>
            <div className="mb-4 inline-flex items-center gap-2 rounded-full border border-orange-300/20 bg-white/8 px-4 py-2">
              <HeartHandshake className="h-4 w-4 text-orange-400" />
              <span className="text-xs font-black uppercase tracking-[0.22em] text-orange-100">
                Keep the rescue chain moving
              </span>
            </div>
            <h3 className="max-w-xl text-3xl font-black tracking-tight text-white">
              More reports answered. More strays protected. More happy homes created.
            </h3>
            <p className="mt-3 max-w-2xl text-sm leading-7 text-slate-300">
              Every volunteer hour, adoption inquiry, and shelter partnership helps turn urgent
              rescues into lasting recoveries.
            </p>
          </div>
          <div className="grid gap-3 self-center text-sm font-bold text-slate-200">
            <div className="rounded-2xl border border-white/10 bg-white/6 px-4 py-3">
              Volunteer response in major city zones
            </div>
            <div className="rounded-2xl border border-white/10 bg-white/6 px-4 py-3">
              Verified shelter coordination and care
            </div>
            <div className="rounded-2xl border border-white/10 bg-white/6 px-4 py-3">
              Adoption support from rescue to homecoming
            </div>
          </div>
        </MotionReveal>

        <MotionReveal className="grid grid-cols-1 gap-8 md:grid-cols-4" delay={0.05} y={22}>
          <div className="col-span-1">
            <div className="mb-4 flex items-center gap-2">
              <div className="rounded-full bg-orange-500 p-2">
                <Heart className="h-6 w-6 fill-current text-white" />
              </div>
              <span className="text-xl font-semibold text-white">
                Stray<span className="text-orange-500">Rescue</span>
              </span>
            </div>
            <p className="text-sm leading-7 text-slate-400">
              Connecting rescued animals with loving homes across Bangladesh. Together, we save
              lives.
            </p>
            <div className="mt-5 inline-flex items-center gap-2 rounded-full border border-white/8 bg-white/4 px-3 py-2 text-xs font-black uppercase tracking-[0.18em] text-orange-200">
              <Heart className="h-3.5 w-3.5 fill-current text-orange-400" />
              Compassion in action
            </div>
          </div>

          <div>
            <h3 className="mb-4 font-semibold text-white">Quick Links</h3>
            <ul className="space-y-2 text-sm">
              {quickLinks.map((item) => (
                <li key={item}>
                  <a className="transition-colors hover:text-orange-400" href="#">
                    {item}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h3 className="mb-4 font-semibold text-white">Get Involved</h3>
            <ul className="space-y-2 text-sm">
              {getInvolved.map((item) => (
                <li key={item}>
                  <a className="transition-colors hover:text-orange-400" href="#">
                    {item}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h3 className="mb-4 font-semibold text-white">Contact</h3>
            <ul className="space-y-3 text-sm">
              <li className="flex items-center gap-2">
                <MapPin className="h-4 w-4 text-orange-500" />
                <span>Dhaka, Bangladesh</span>
              </li>
              <li className="flex items-center gap-2">
                <Mail className="h-4 w-4 text-orange-500" />
                <span>info@strayrescue.bd</span>
              </li>
              <li className="flex items-center gap-2">
                <Phone className="h-4 w-4 text-orange-500" />
                <span>+880 1234-567890</span>
              </li>
            </ul>

            <div className="mt-4 flex gap-3">
              <a
                aria-label="Community updates"
                className="rounded-xl bg-slate-800 p-2 transition-colors hover:bg-orange-500"
                href="#"
              >
                <Earth className="h-5 w-5" />
              </a>
              <a
                aria-label="Rescue stories"
                className="rounded-xl bg-slate-800 p-2 transition-colors hover:bg-orange-500"
                href="#"
              >
                <MessageCircleHeart className="h-5 w-5" />
              </a>
              <a
                aria-label="Share updates"
                className="rounded-xl bg-slate-800 p-2 transition-colors hover:bg-orange-500"
                href="#"
              >
                <SendHorizontal className="h-5 w-5" />
              </a>
            </div>
          </div>
        </MotionReveal>

        <MotionReveal
          className="mt-8 flex flex-col gap-3 border-t border-slate-800 pt-8 text-sm text-slate-500 md:flex-row md:items-center md:justify-between"
          delay={0.08}
          y={18}
        >
          <p>
            &copy; 2026 StrayRescue Bangladesh. All rights reserved. Made with{' '}
            <Heart className="inline h-4 w-4 fill-current text-orange-500" /> for animals in
            need.
          </p>
          <div className="flex flex-wrap gap-4 text-xs font-bold uppercase tracking-[0.18em] text-slate-500">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">Support</a>
          </div>
        </MotionReveal>
      </div>
    </footer>
  )
}

export default Footer

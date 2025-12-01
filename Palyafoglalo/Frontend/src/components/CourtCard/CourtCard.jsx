/**
 * Court Card Component
 * Modern card displaying court information with images, reviews, and pricing
 */

import { Link } from 'react-router-dom';
import { useState, useEffect } from 'react';
import { MapPin, Sun, Moon, Lightbulb, ArrowRight, Calendar, Star, Users } from 'lucide-react';
import { pricingAPI, courtReviewsAPI } from '../../services/api';
import { formatPrice } from '../../utils/format';

const CourtCard = ({ court }) => {
  const [prices, setPrices] = useState({});
  const [loadingPrices, setLoadingPrices] = useState(true);
  const [reviewStats, setReviewStats] = useState(null);

  useEffect(() => {
    // Fixed prices - always use these values
    const fixedPrices = {
      1: '9 000',
      2: '16 000',
      3: '20 000',
      currency: 'HUF',
    };
    
    // Set fixed prices immediately
    setPrices(fixedPrices);
    setLoadingPrices(false);

    const fetchStats = async () => {
      try {
        const response = await courtReviewsAPI.stats(court.id);
        if (response.success) {
          setReviewStats(response.data);
        }
      } catch (err) {
        // Ignore errors
      }
    };

    fetchStats();
  }, [court.id]);

  const addHours = (dateStr, hours) => {
    const date = new Date(dateStr);
    date.setHours(date.getHours() + hours);
    return date.toISOString().slice(0, 19).replace('T', ' ');
  };

  const mainImage = court.main_image_url || 
    (court.images && court.images.length > 0 ? court.images[0].image_url : null);

  return (
    <div className="card group hover:shadow-xl transition-all duration-300 overflow-hidden">
      {/* Image */}
      {mainImage && (
        <div className="relative h-48 overflow-hidden bg-gray-200 -mx-6 -mt-6 mb-4">
          <img
            src={mainImage}
            alt={court.name}
            className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            onError={(e) => {
              e.target.style.display = 'none';
            }}
          />
          {/* Rating badge overlay */}
          {reviewStats && reviewStats.average_rating > 0 && (
            <div className="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-lg">
              <Star className="h-4 w-4 text-yellow-500 fill-yellow-500" />
              <span className="font-bold text-sm">{reviewStats.average_rating.toFixed(1)}</span>
              {reviewStats.total_reviews > 0 && (
                <span className="text-xs text-gray-600">({reviewStats.total_reviews})</span>
              )}
            </div>
          )}
        </div>
      )}

      {/* Header */}
      <div className="flex items-start justify-between mb-3">
        <div className="flex-1">
          <div className="flex items-center gap-2 mb-2 flex-wrap">
            <h3 className="text-xl font-bold text-gray-900 group-hover:text-primary-600 transition-colors">
              {court.name}
            </h3>
            <span className="badge badge-info text-xs">
              {court.surface_name || court.surface}
            </span>
          </div>
          
          {/* Features */}
          <div className="flex flex-wrap items-center gap-2 text-xs mb-3">
            {court.is_indoor ? (
              <span className="flex items-center text-gray-600 bg-blue-50 px-2 py-1 rounded">
                <Moon className="h-3 w-3 mr-1 text-blue-600" />
                Beltéri
              </span>
            ) : (
              <span className="flex items-center text-gray-600 bg-green-50 px-2 py-1 rounded">
                <Sun className="h-3 w-3 mr-1 text-green-600" />
                Kültéri
              </span>
            )}
            {court.has_lighting && (
              <span className="flex items-center text-gray-600 bg-yellow-50 px-2 py-1 rounded">
                <Lightbulb className="h-3 w-3 mr-1 text-yellow-600" />
                Világítás
              </span>
            )}
          </div>
        </div>
      </div>

      {/* Description */}
      {court.description ? (
        <p className="text-sm text-gray-600 mb-4 line-clamp-2">{court.description}</p>
      ) : court.notes ? (
        <p className="text-sm text-gray-600 mb-4 line-clamp-2">{court.notes}</p>
      ) : null}

      {/* Pricing - Always show fixed prices */}
      <div className="bg-gradient-to-r from-primary-50 to-blue-50 rounded-xl p-4 mb-4 border border-primary-100">
        <div className="text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Árak</div>
        <div className="grid grid-cols-3 gap-2">
          <div className="text-center">
            <div className="text-xs text-gray-600">1 óra</div>
            <div className="font-bold text-primary-600">{prices[1] || '9 000'} {prices.currency || 'HUF'}</div>
          </div>
          <div className="text-center">
            <div className="text-xs text-gray-600">2 óra</div>
            <div className="font-bold text-primary-600">{prices[2] || '16 000'} {prices.currency || 'HUF'}</div>
          </div>
          <div className="text-center">
            <div className="text-xs text-gray-600">3 óra</div>
            <div className="font-bold text-primary-600">{prices[3] || '20 000'} {prices.currency || 'HUF'}</div>
          </div>
        </div>
      </div>

      {/* Reviews preview */}
      {reviewStats && reviewStats.total_reviews > 0 && (
        <div className="flex items-center gap-2 text-sm text-gray-600 mb-4">
          <Star className="h-4 w-4 text-yellow-500 fill-yellow-500" />
          <span className="font-medium">{reviewStats.average_rating.toFixed(1)}</span>
          <span className="text-gray-500">({reviewStats.total_reviews} értékelés)</span>
        </div>
      )}

      {/* Action Buttons */}
      <div className="flex items-center gap-3 pt-4 border-t border-gray-100">
        <Link
          to={`/courts/${court.id}`}
          className="btn btn-secondary flex-1 flex items-center justify-center text-sm py-2"
          onClick={(e) => e.stopPropagation()}
        >
          Részletek
          <ArrowRight className="h-4 w-4 ml-1" />
        </Link>
        <Link
          to={`/book/${court.id}`}
          className="btn btn-primary flex-1 flex items-center justify-center text-sm py-2"
          onClick={(e) => e.stopPropagation()}
        >
          <Calendar className="h-4 w-4 mr-1" />
          Foglalás
        </Link>
      </div>
    </div>
  );
};

export default CourtCard;


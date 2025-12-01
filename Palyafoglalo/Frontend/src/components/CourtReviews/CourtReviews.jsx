/**
 * Court Reviews Component
 * Displays and manages court reviews/ratings
 */

import { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { courtReviewsAPI } from '../../services/api';
import { Star, CheckCircle, Edit2, Trash2, Send } from 'lucide-react';

const CourtReviews = ({ courtId }) => {
  const { isAuthenticated, user } = useAuth();
  const [reviews, setReviews] = useState([]);
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editingReview, setEditingReview] = useState(null);
  const [formData, setFormData] = useState({
    rating: 5,
    title: '',
    review_text: '',
  });
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchReviews();
    fetchStats();
  }, [courtId]);

  const fetchReviews = async () => {
    try {
      setLoading(true);
      const response = await courtReviewsAPI.list(courtId, { limit: 20 });
      if (response.success) {
        setReviews(response.data?.reviews || []);
      }
    } catch (err) {
      console.error('Failed to fetch reviews:', err);
    } finally {
      setLoading(false);
    }
  };

  const fetchStats = async () => {
    try {
      const response = await courtReviewsAPI.stats(courtId);
      if (response.success) {
        setStats(response.data);
      }
    } catch (err) {
      console.error('Failed to fetch stats:', err);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSubmitting(true);
    setError(null);

    try {
      let response;
      if (editingReview) {
        response = await courtReviewsAPI.update(editingReview.id, formData);
      } else {
        response = await courtReviewsAPI.create(courtId, formData);
      }

      if (response.success) {
        setFormData({ rating: 5, title: '', review_text: '' });
        setShowForm(false);
        setEditingReview(null);
        await fetchReviews();
        await fetchStats();
      } else {
        setError(response.message || 'Hiba történt');
      }
    } catch (err) {
      setError(err.message || 'Hiba történt');
    } finally {
      setSubmitting(false);
    }
  };

  const handleDelete = async (reviewId) => {
    if (!confirm('Biztosan törölni szeretnéd ezt a véleményt?')) return;

    try {
      const response = await courtReviewsAPI.delete(reviewId);
      if (response.success) {
        await fetchReviews();
        await fetchStats();
      }
    } catch (err) {
      alert('Hiba történt a törlés során');
    }
  };

  const startEdit = (review) => {
    setEditingReview(review);
    setFormData({
      rating: review.rating,
      title: review.title || '',
      review_text: review.review_text || '',
    });
    setShowForm(true);
  };

  const cancelEdit = () => {
    setEditingReview(null);
    setFormData({ rating: 5, title: '', review_text: '' });
    setShowForm(false);
  };

  const userReview = reviews.find((r) => r.user_id === user?.id);

  return (
    <div className="space-y-6">
      {/* Stats Section */}
      {stats && (
        <div className="card p-6">
          <div className="flex items-center justify-between flex-wrap gap-4">
            <div className="flex items-center gap-4">
              <div className="text-center">
                <div className="text-4xl font-bold text-primary-600">
                  {stats.average_rating?.toFixed(1) || '0.0'}
                </div>
                <div className="flex gap-1 mt-1">
                  {[1, 2, 3, 4, 5].map((star) => (
                    <Star
                      key={star}
                      className={`h-5 w-5 ${
                        star <= Math.round(stats.average_rating || 0)
                          ? 'fill-yellow-400 text-yellow-400'
                          : 'text-gray-300'
                      }`}
                    />
                  ))}
                </div>
                <div className="text-sm text-gray-600 mt-2">
                  {stats.total_reviews || 0} értékelés
                </div>
              </div>

              <div className="border-l pl-4 space-y-2">
                {[5, 4, 3, 2, 1].map((rating) => {
                  const count = stats[`${rating}_star`] || 0;
                  const percentage = stats.total_reviews > 0 
                    ? (count / stats.total_reviews) * 100 
                    : 0;
                  return (
                    <div key={rating} className="flex items-center gap-2 text-sm">
                      <span className="w-8">{rating} ⭐</span>
                      <div className="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div
                          className="h-full bg-primary-500 rounded-full"
                          style={{ width: `${percentage}%` }}
                        />
                      </div>
                      <span className="w-8 text-gray-600">{count}</span>
                    </div>
                  );
                })}
              </div>
            </div>

            {isAuthenticated() && !userReview && !showForm && (
              <button
                onClick={() => setShowForm(true)}
                className="btn btn-primary"
              >
                Vélemény írása
              </button>
            )}
          </div>
        </div>
      )}

      {/* Review Form */}
      {showForm && isAuthenticated() && (
        <div className="card p-6">
          <h3 className="text-xl font-bold mb-4">
            {editingReview ? 'Vélemény szerkesztése' : 'Új vélemény'}
          </h3>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium mb-2">Értékelés</label>
              <div className="flex gap-2">
                {[1, 2, 3, 4, 5].map((rating) => (
                  <button
                    key={rating}
                    type="button"
                    onClick={() => setFormData({ ...formData, rating })}
                    className={`p-2 rounded-lg transition-all ${
                      formData.rating >= rating
                        ? 'bg-yellow-100 text-yellow-600'
                        : 'bg-gray-100 text-gray-400 hover:bg-gray-200'
                    }`}
                  >
                    <Star
                      className={`h-6 w-6 ${
                        formData.rating >= rating ? 'fill-current' : ''
                      }`}
                    />
                  </button>
                ))}
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium mb-2">Cím (opcionális)</label>
              <input
                type="text"
                value={formData.title}
                onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                className="input"
                placeholder="Rövid cím..."
              />
            </div>

            <div>
              <label className="block text-sm font-medium mb-2">Vélemény</label>
              <textarea
                value={formData.review_text}
                onChange={(e) => setFormData({ ...formData, review_text: e.target.value })}
                className="input"
                rows="4"
                placeholder="Írd le a tapasztalataid..."
                required
              />
            </div>

            {error && (
              <div className="p-3 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
                {error}
              </div>
            )}

            <div className="flex gap-3">
              <button
                type="submit"
                disabled={submitting}
                className="btn btn-primary flex items-center gap-2"
              >
                <Send className="h-4 w-4" />
                {submitting ? 'Küldés...' : editingReview ? 'Mentés' : 'Küldés'}
              </button>
              <button
                type="button"
                onClick={cancelEdit}
                className="btn btn-secondary"
              >
                Mégse
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Reviews List */}
      <div className="space-y-4">
        <h3 className="text-2xl font-bold">Vélemények</h3>
        {loading ? (
          <div className="text-center py-8">Betöltés...</div>
        ) : reviews.length === 0 ? (
          <div className="card p-8 text-center text-gray-500">
            Még nincsenek vélemények. Legyél te az első!
          </div>
        ) : (
          reviews.map((review) => (
            <div key={review.id} className="card p-6">
              <div className="flex items-start justify-between mb-3">
                <div className="flex items-start gap-3">
                  <div className="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center font-bold text-primary-700">
                    {review.user_name?.charAt(0).toUpperCase() || 'U'}
                  </div>
                  <div>
                    <div className="flex items-center gap-2">
                      <span className="font-semibold">{review.user_name || 'Felhasználó'}</span>
                      {review.is_verified && (
                        <CheckCircle className="h-4 w-4 text-green-500" title="Ellenőrzött foglalás" />
                      )}
                    </div>
                    <div className="flex items-center gap-1 mt-1">
                      {[1, 2, 3, 4, 5].map((star) => (
                        <Star
                          key={star}
                          className={`h-4 w-4 ${
                            star <= review.rating
                              ? 'fill-yellow-400 text-yellow-400'
                              : 'text-gray-300'
                          }`}
                        />
                      ))}
                      <span className="text-sm text-gray-500 ml-2">
                        {new Date(review.created_at).toLocaleDateString('hu-HU')}
                      </span>
                    </div>
                  </div>
                </div>

                {user && user.id === review.user_id && (
                  <div className="flex gap-2">
                    <button
                      onClick={() => startEdit(review)}
                      className="p-2 text-gray-600 hover:text-primary-600 transition-colors"
                      title="Szerkesztés"
                    >
                      <Edit2 className="h-4 w-4" />
                    </button>
                    <button
                      onClick={() => handleDelete(review.id)}
                      className="p-2 text-gray-600 hover:text-red-600 transition-colors"
                      title="Törlés"
                    >
                      <Trash2 className="h-4 w-4" />
                    </button>
                  </div>
                )}
              </div>

              {review.title && (
                <h4 className="font-semibold text-lg mb-2">{review.title}</h4>
              )}

              {review.review_text && (
                <p className="text-gray-700 whitespace-pre-wrap">{review.review_text}</p>
              )}
            </div>
          ))
        )}
      </div>
    </div>
  );
};

export default CourtReviews;

